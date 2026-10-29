<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Sheets Integration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Google Sheets API sebagai database cloud
    | sekunder. Data transaksi akan otomatis disinkronkan ke spreadsheet.
    |
    */

    'enabled' => env('GOOGLE_SHEETS_ENABLED', false),

    // Path ke file JSON Service Account Google Cloud (relatif terhadap root project atau absolut)
    'service_account_json' => (function () {
        $path = env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', 'storage/app/google-credentials.json');
        return str_starts_with($path, '/') || preg_match('/^[A-Za-z]:/', $path)
            ? $path
            : base_path($path);
    })(),

    // Spreadsheet ID (diambil dari URL Google Spreadsheet)
    'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID', ''),

    // Nama sheet untuk data transaksi dan detail item
    'sheets' => [
        'transactions' => env('GOOGLE_SHEETS_TRANSACTION_SHEET', 'Transaksi'),
        'items'        => env('GOOGLE_SHEETS_ITEMS_SHEET', 'Detail Item'),
    ],

];
