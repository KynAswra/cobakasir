<?php

namespace Midtrans;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Config
{
    public static $serverKey;
    public static $clientKey;
    public static $isProduction = false;
    public static $isSanitized = true;
    public static $is3ds = true;
}

class Snap
{
    public static function getSnapToken($params)
    {
        $serverKey = Config::$serverKey ?: env('MIDTRANS_SERVER_KEY');
        $isProduction = Config::$isProduction;

        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        Log::info('Midtrans Shim: Using server key = [' . substr($serverKey, 0, 15) . '...], Sandbox = ' . ($isProduction ? 'NO (Production)' : 'YES'));

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withBasicAuth($serverKey, '')
            ->post($snapUrl, $params);

        if ($response->failed()) {
            $errorData = $response->json() ?? ['raw' => $response->body()];
            Log::error('Midtrans API Error (via Shim)', [
                'status' => $response->status(),
                'error' => $errorData,
                'payload' => $params,
            ]);

            $msg = $errorData['error_messages'][0] ?? 'Gagal menghubungkan ke Midtrans.';
            throw new \Exception($msg);
        }

        $result = $response->json();

        if (empty($result['token'])) {
            throw new \Exception('Respon dari Midtrans tidak menyertakan token transaksi.');
        }

        return $result['token'];
    }
}
