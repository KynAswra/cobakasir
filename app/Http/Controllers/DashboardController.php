<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'month'); // default to 'month'

        $query = Transaction::query();
        $itemQuery = TransactionItem::query();

        if ($period === 'today') {
            $query->whereDate('created_at', today());
            $itemQuery->whereHas('transaction', fn($q) => $q->whereDate('created_at', today()));
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $itemQuery->whereHas('transaction', fn($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year));
        }

        $totalRevenue = $query->sum('total');
        $totalTransactions = (clone $query)->count();
        $avgTransaction = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions) : 0;

        $bestSeller = (clone $itemQuery)
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->with('menu')
            ->first();

        $transactions = Transaction::with(['items.menu', 'user'])
            ->latest()
            ->paginate(10);

        $menuPopularity = (clone $itemQuery)
            ->select('menu_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->with('menu')
            ->limit(5)
            ->get();

        $maxQty = $menuPopularity->max('total_qty') ?: 1;

        return view('dashboard.index', compact(
            'totalRevenue',
            'totalTransactions',
            'avgTransaction',
            'bestSeller',
            'transactions',
            'menuPopularity',
            'maxQty',
            'period'
        ));
    }
}
