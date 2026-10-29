<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getReportData(Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $transactions = Transaction::with(['items.menu', 'user'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $avgTransaction = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions) : 0;

        $menuPopularity = TransactionItem::query()
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->whereHas('transaction', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->with('menu')
            ->get();

        $bestSeller = $menuPopularity->first();

        $paymentBreakdown = $transactions->groupBy('payment_method')->map->count();

        return compact(
            'from', 'to', 'transactions', 'totalRevenue', 'totalTransactions',
            'avgTransaction', 'menuPopularity', 'bestSeller', 'paymentBreakdown'
        );
    }

    public function toCsv(array $data): string
    {
        $lines = [];
        $from = $data['from']->format('d/m/Y');
        $to = $data['to']->format('d/m/Y');

        $lines[] = $this->csvRow(['Laporan Penjualan - Gorengan Mama Ilham']);
        $lines[] = $this->csvRow(['Periode', "{$from} s/d {$to}"]);
        $lines[] = $this->csvRow(['Dicetak', now()->format('d/m/Y H:i')]);
        $lines[] = '';

        $lines[] = $this->csvRow(['RINGKASAN']);
        $lines[] = $this->csvRow(['Total Pendapatan', $data['totalRevenue']]);
        $lines[] = $this->csvRow(['Total Transaksi', $data['totalTransactions']]);
        $lines[] = $this->csvRow(['Rata-rata per Transaksi', $data['avgTransaction']]);
        $lines[] = $this->csvRow(['Menu Terlaris', $data['bestSeller']?->menu?->name ?? '-']);
        $lines[] = $this->csvRow(['Porsi Terlaris', $data['bestSeller']?->total_qty ?? 0]);
        $lines[] = $this->csvRow(['Pembayaran Tunai', $data['paymentBreakdown']['tunai'] ?? 0]);
        $lines[] = $this->csvRow(['Pembayaran QRIS', $data['paymentBreakdown']['qris'] ?? 0]);
        $lines[] = '';

        $lines[] = $this->csvRow(['DAFTAR TRANSAKSI']);
        $lines[] = $this->csvRow(['No Struk', 'Tanggal', 'Waktu', 'Kasir', 'Item', 'Pembayaran', 'Total (Rp)']);

        foreach ($data['transactions'] as $trx) {
            $items = $trx->items->map(fn ($i) => $i->menu->name . ' x' . $i->qty)->join(', ');
            $lines[] = $this->csvRow([
                $trx->receipt_number,
                $trx->created_at->format('d/m/Y'),
                $trx->created_at->format('H:i'),
                $trx->user->name,
                $items,
                $trx->payment_method === 'qris' ? 'QRIS' : 'Tunai',
                $trx->total,
            ]);
        }

        $lines[] = '';

        $lines[] = $this->csvRow(['POPULARITAS MENU']);
        $lines[] = $this->csvRow(['Menu', 'Jumlah Terjual', 'Total Omset (Rp)']);

        foreach ($data['menuPopularity'] as $item) {
            $lines[] = $this->csvRow([
                $item->menu->name,
                $item->total_qty,
                $item->total_sales,
            ]);
        }

        $lines[] = '';

        $lines[] = $this->csvRow(['DETAIL ITEM PER TRANSAKSI']);
        $lines[] = $this->csvRow(['No Struk', 'Tanggal', 'Menu', 'Qty', 'Harga (Rp)', 'Subtotal (Rp)']);

        foreach ($data['transactions'] as $trx) {
            foreach ($trx->items as $item) {
                $lines[] = $this->csvRow([
                    $trx->receipt_number,
                    $trx->created_at->format('d/m/Y'),
                    $item->menu->name,
                    $item->qty,
                    $item->price,
                    $item->subtotal,
                ]);
            }
        }

        return "\xEF\xBB\xBF" . implode("\n", $lines);
    }

    private function csvRow(array $fields): string
    {
        return collect($fields)->map(function ($field) {
            $field = (string) $field;
            if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                return '"' . str_replace('"', '""', $field) . '"';
            }

            return $field;
        })->implode(',');
    }
}
