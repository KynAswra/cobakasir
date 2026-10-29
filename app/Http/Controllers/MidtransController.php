<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function getSnapToken(Request $request)
    {
        $request->validate([
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.menu_id'        => ['required', 'integer', 'exists:menus,id'],
            'items.*.qty'            => ['required', 'integer', 'min:1'],
        ]);

        $menuIds = collect($request->items)->pluck('menu_id');
        $menus = Menu::whereIn('id', $menuIds)->where('is_active', true)->get()->keyBy('id');

        if ($menus->count() !== $menuIds->unique()->count()) {
            return response()->json(['message' => 'Menu tidak valid.'], 422);
        }

        $total = 0;
        $itemDetails = [];

        foreach ($request->items as $item) {
            $menu = $menus[$item['menu_id']];
            $subtotal = $menu->price * $item['qty'];
            $total += $subtotal;

            $itemDetails[] = [
                'id'       => (string) $menu->id,
                'price'    => (int) $menu->price,
                'quantity' => (int) $item['qty'],
                'name'     => (string) Str::limit($menu->name, 50, ''), // Midtrans limit is 50 chars
            ];
        }

        // Load Midtrans SDK Shim
        require_once app_path('MidtransShim.php');

        // Set konfigurasi Midtrans dari .env
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false) === true || env('MIDTRANS_IS_PRODUCTION', false) === 'true';
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Order ID must be unique
        $orderId = 'TRX-' . time() . '-' . rand(100, 999);

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $total,
                'currency'     => 'IDR',
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => auth()->user()->name ?? 'Kasir',
                'email'      => auth()->user()->email ?? 'kasir@mamailham.com',
            ],
            // 'enabled_payments' => ['qris'],
        ];

        try {
            // Memanggil SDK / Shim untuk generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($payload);

            return response()->json([
                'snap_token'   => $snapToken,
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken,
                'order_id'     => $orderId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghubungkan ke Midtrans.',
                'error'   => ['error_messages' => [$e->getMessage()]]
            ], 500);
        }
    }
}
