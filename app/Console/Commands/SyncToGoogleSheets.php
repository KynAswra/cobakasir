<?php

namespace App\Console\Commands;

use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class SyncToGoogleSheets extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sheets:sync
                            {--fresh : Hapus data lama dan sync ulang dari awal}';

    /**
     * The console command description.
     */
    protected $description = 'Sinkronkan semua data transaksi dari MySQL ke Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle(GoogleSheetsService $sheetsService): int
    {
        if (!$sheetsService->isEnabled()) {
            $this->error('❌ Google Sheets belum dikonfigurasi.');
            $this->newLine();
            $this->warn('Pastikan variabel berikut sudah diset di file .env:');
            $this->line('  GOOGLE_SHEETS_ENABLED=true');
            $this->line('  GOOGLE_SERVICE_ACCOUNT_JSON_PATH=/path/to/credentials.json');
            $this->line('  GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id');
            return Command::FAILURE;
        }

        $this->info('🔄 Memulai sinkronisasi ke Google Sheets...');
        $this->newLine();

        try {
            $count = $sheetsService->syncAllTransactions();

            $this->newLine();
            $this->info("✅ Berhasil! {$count} transaksi telah disinkronkan ke Google Sheets.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Gagal sinkronisasi: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
