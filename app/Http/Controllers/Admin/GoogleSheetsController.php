<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetsService;

class GoogleSheetsController extends Controller
{
    public function __construct(private GoogleSheetsService $sheetsService) {}

    /**
     * Sinkronkan semua transaksi ke Google Sheets.
     * Endpoint: POST /admin/sheets/sync
     */
    public function sync()
    {
        if (!$this->sheetsService->isEnabled()) {
            return back()->with('error', 'Google Sheets belum dikonfigurasi. Periksa file .env');
        }

        try {
            $count = $this->sheetsService->syncAllTransactions();

            return back()->with('success', "✅ {$count} transaksi berhasil disinkronkan ke Google Sheets!");
        } catch (\Exception $e) {
            return back()->with('error', "❌ Gagal sinkronisasi: {$e->getMessage()}");
        }
    }
}
