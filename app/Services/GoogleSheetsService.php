<?php

namespace App\Services;

use App\Models\Transaction;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request as SheetsRequest;
use Google\Service\Sheets\AddSheetRequest;
use Google\Service\Sheets\SheetProperties;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private ?GoogleSheets $sheetsService = null;
    private string $spreadsheetId;
    private string $transactionSheet;
    private string $itemsSheet;

    public function __construct()
    {
        $this->spreadsheetId = config('google-sheets.spreadsheet_id');
        $this->transactionSheet = config('google-sheets.sheets.transactions');
        $this->itemsSheet = config('google-sheets.sheets.items');
    }

    /**
     * Cek apakah integrasi Google Sheets aktif dan terkonfigurasi.
     */
    public function isEnabled(): bool
    {
        return config('google-sheets.enabled', false)
            && !empty($this->spreadsheetId)
            && file_exists(config('google-sheets.service_account_json'));
    }

    /**
     * Mendapatkan instance Google Sheets service (lazy-loaded).
     */
    private function getSheetsService(): GoogleSheets
    {
        if ($this->sheetsService === null) {
            $client = new GoogleClient();
            $client->setApplicationName('Kasir Mama Ilham');
            $client->setScopes([GoogleSheets::SPREADSHEETS]);
            $client->setAuthConfig(config('google-sheets.service_account_json'));

            $this->sheetsService = new GoogleSheets($client);
        }

        return $this->sheetsService;
    }

    /**
     * Inisialisasi header kolom pada kedua sheet.
     * Dipanggil sekali saat setup awal atau saat sync bulk.
     */
    public function initializeHeaders(): void
    {
        $service = $this->getSheetsService();

        // Pastikan kedua sheet ada
        $this->ensureSheetExists($this->transactionSheet);
        $this->ensureSheetExists($this->itemsSheet);

        // Header sheet Transaksi
        $transactionHeaders = [
            ['No. Nota', 'Kasir', 'Total (Rp)', 'Metode Bayar', 'Uang Diterima (Rp)', 'Kembalian (Rp)', 'Tanggal & Waktu']
        ];

        $service->spreadsheets_values->update(
            $this->spreadsheetId,
            "{$this->transactionSheet}!A1:G1",
            new ValueRange(['values' => $transactionHeaders]),
            ['valueInputOption' => 'RAW']
        );

        // Header sheet Detail Item
        $itemHeaders = [
            ['No. Nota', 'Nama Menu', 'Qty', 'Harga Satuan (Rp)', 'Subtotal (Rp)']
        ];

        $service->spreadsheets_values->update(
            $this->spreadsheetId,
            "{$this->itemsSheet}!A1:E1",
            new ValueRange(['values' => $itemHeaders]),
            ['valueInputOption' => 'RAW']
        );

        Log::info('Google Sheets: Headers berhasil diinisialisasi.');
    }

    /**
     * Menambahkan data transaksi baru ke Google Sheets.
     * Dipanggil setiap kali transaksi berhasil disimpan ke MySQL.
     */
    public function appendTransaction(Transaction $transaction): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $transaction->loadMissing(['items.menu', 'user']);
            $service = $this->getSheetsService();

            // Append baris transaksi
            $transactionRow = [[
                $transaction->receipt_number,
                $transaction->user->name ?? 'Unknown',
                $transaction->total,
                ucfirst($transaction->payment_method),
                $transaction->cash_received ?? '-',
                $transaction->change_amount ?? '-',
                $transaction->created_at->format('Y-m-d H:i:s'),
            ]];

            $service->spreadsheets_values->append(
                $this->spreadsheetId,
                "{$this->transactionSheet}!A:G",
                new ValueRange(['values' => $transactionRow]),
                ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
            );

            // Append detail item
            $itemRows = [];
            foreach ($transaction->items as $item) {
                $itemRows[] = [
                    $transaction->receipt_number,
                    $item->menu->name ?? 'Menu Dihapus',
                    $item->qty,
                    $item->price,
                    $item->subtotal,
                ];
            }

            if (!empty($itemRows)) {
                $service->spreadsheets_values->append(
                    $this->spreadsheetId,
                    "{$this->itemsSheet}!A:E",
                    new ValueRange(['values' => $itemRows]),
                    ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
                );
            }

            Log::info("Google Sheets: Transaksi {$transaction->receipt_number} berhasil disinkronkan.");

        } catch (\Exception $e) {
            // Fail-safe: log error tapi jangan ganggu transaksi MySQL
            Log::error("Google Sheets sync gagal untuk transaksi {$transaction->receipt_number}: {$e->getMessage()}");
        }
    }

    /**
     * Sinkronkan semua transaksi dari MySQL ke Google Sheets (bulk).
     * Menghapus data lama dan menulis ulang semuanya.
     *
     * @return int Jumlah transaksi yang berhasil disinkronkan
     */
    public function syncAllTransactions(): int
    {
        $service = $this->getSheetsService();

        // Hapus data lama (kecuali header)
        $this->clearSheetData($this->transactionSheet);
        $this->clearSheetData($this->itemsSheet);

        // Inisialisasi header
        $this->initializeHeaders();

        // Ambil semua transaksi
        $transactions = Transaction::with(['items.menu', 'user'])
            ->orderBy('id')
            ->get();

        if ($transactions->isEmpty()) {
            Log::info('Google Sheets: Tidak ada transaksi untuk disinkronkan.');
            return 0;
        }

        // Siapkan data transaksi
        $transactionRows = [];
        $itemRows = [];

        foreach ($transactions as $transaction) {
            $transactionRows[] = [
                $transaction->receipt_number,
                $transaction->user->name ?? 'Unknown',
                $transaction->total,
                ucfirst($transaction->payment_method),
                $transaction->cash_received ?? '-',
                $transaction->change_amount ?? '-',
                $transaction->created_at->format('Y-m-d H:i:s'),
            ];

            foreach ($transaction->items as $item) {
                $itemRows[] = [
                    $transaction->receipt_number,
                    $item->menu->name ?? 'Menu Dihapus',
                    $item->qty,
                    $item->price,
                    $item->subtotal,
                ];
            }
        }

        // Batch append transaksi
        if (!empty($transactionRows)) {
            $service->spreadsheets_values->append(
                $this->spreadsheetId,
                "{$this->transactionSheet}!A:G",
                new ValueRange(['values' => $transactionRows]),
                ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
            );
        }

        // Batch append items
        if (!empty($itemRows)) {
            $service->spreadsheets_values->append(
                $this->spreadsheetId,
                "{$this->itemsSheet}!A:E",
                new ValueRange(['values' => $itemRows]),
                ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
            );
        }

        Log::info("Google Sheets: {$transactions->count()} transaksi berhasil disinkronkan.");

        return $transactions->count();
    }

    /**
     * Memastikan sheet dengan nama tertentu ada di spreadsheet.
     * Jika belum ada, buat sheet baru.
     */
    private function ensureSheetExists(string $sheetTitle): void
    {
        $service = $this->getSheetsService();
        $spreadsheet = $service->spreadsheets->get($this->spreadsheetId);

        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === $sheetTitle) {
                return; // Sheet sudah ada
            }
        }

        // Buat sheet baru
        $addSheetRequest = new SheetsRequest([
            'addSheet' => new AddSheetRequest([
                'properties' => new SheetProperties([
                    'title' => $sheetTitle,
                ]),
            ]),
        ]);

        $batchRequest = new BatchUpdateSpreadsheetRequest([
            'requests' => [$addSheetRequest],
        ]);

        $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchRequest);

        Log::info("Google Sheets: Sheet '{$sheetTitle}' berhasil dibuat.");
    }

    /**
     * Hapus semua data pada sheet (kecuali header row 1).
     */
    private function clearSheetData(string $sheetTitle): void
    {
        try {
            $service = $this->getSheetsService();
            $this->ensureSheetExists($sheetTitle);

            $service->spreadsheets_values->clear(
                $this->spreadsheetId,
                "{$sheetTitle}!A:Z",
                new \Google\Service\Sheets\ClearValuesRequest()
            );
        } catch (\Exception $e) {
            Log::warning("Google Sheets: Gagal menghapus data sheet '{$sheetTitle}': {$e->getMessage()}");
        }
    }
}
