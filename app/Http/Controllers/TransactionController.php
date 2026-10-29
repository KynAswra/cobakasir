<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaction;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function __construct(private GoogleSheetsService $sheetsService) {}

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.menu', 'user']);

        return response()->json($this->formatTransaction($transaction));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.menu_id'        => ['required', 'integer', 'exists:menus,id'],
            'items.*.qty'            => ['required', 'integer', 'min:1'],
            'payment_method'         => ['required', 'in:tunai,qris'],
            'cash_received'          => ['nullable', 'required_if:payment_method,tunai', 'integer', 'min:0'],
        ]);

        $menuIds = collect($data['items'])->pluck('menu_id');
        $menus = Menu::whereIn('id', $menuIds)->where('is_active', true)->get()->keyBy('id');

        if ($menus->count() !== $menuIds->unique()->count()) {
            return response()->json(['message' => 'Menu tidak valid.'], 422);
        }

        $transaction = DB::transaction(function () use ($data, $menus) {
            $total = 0;
            $lineItems = [];

            foreach ($data['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $subtotal = $menu->price * $item['qty'];
                $total += $subtotal;

                $lineItems[] = [
                    'menu_id'  => $menu->id,
                    'qty'      => $item['qty'],
                    'price'    => $menu->price,
                    'subtotal' => $subtotal,
                ];
            }

            $lastId = Transaction::max('id') ?? 0;
            $cashReceived = $data['payment_method'] === 'tunai' ? (int) $data['cash_received'] : null;

            if ($cashReceived !== null && $cashReceived < $total) {
                throw ValidationException::withMessages([
                    'cash_received' => 'Uang tunai kurang dari total pembayaran.',
                ]);
            }

            $transaction = Transaction::create([
                'receipt_number' => '#' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT),
                'user_id'        => auth()->id(),
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'cash_received'  => $cashReceived,
                'change_amount'  => $cashReceived !== null ? $cashReceived - $total : null,
            ]);

            foreach ($lineItems as $line) {
                $transaction->items()->create($line);
            }

            return $transaction;
        });

        $transaction->load(['items.menu', 'user']);

        // Sync ke Google Sheets (fail-safe: tidak mengganggu transaksi jika gagal)
        try {
            $this->sheetsService->appendTransaction($transaction);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Google Sheets sync error: {$e->getMessage()}");
        }

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            ...$this->formatTransaction($transaction),
        ]);
    }

    private function formatTransaction(Transaction $transaction): array
    {
        return [
            'id'              => $transaction->id,
            'receipt_number'  => $transaction->receipt_number,
            'total'           => $transaction->total,
            'payment_method'  => $transaction->payment_method,
            'cash_received'   => $transaction->cash_received,
            'change_amount'   => $transaction->change_amount,
            'created_at'      => $transaction->created_at->format('d/m/Y H:i'),
            'cashier'         => $transaction->user->name,
            'items'           => $transaction->items->map(fn ($i) => [
                'name'     => $i->menu->name,
                'qty'      => $i->qty,
                'price'    => $i->price,
                'subtotal' => $i->subtotal,
            ])->values(),
        ];
    }
}
