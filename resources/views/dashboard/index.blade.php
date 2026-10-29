@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan penjualan dan performa warung')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(139, 92, 246, 0.1);
        box-shadow: 0 4px 20px rgba(109, 40, 217, 0.05);
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(109, 40, 217, 0.1);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-violet-gradient {
        background: linear-gradient(135deg, #8b5cf6, #c026d3);
        color: white;
        box-shadow: 0 4px 14px rgba(139, 92, 246, 0.3);
    }

    .bg-emerald-gradient {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }

    .bg-amber-gradient {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
    }

    .bg-blue-gradient {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    }

    .table-container {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(139, 92, 246, 0.1);
        box-shadow: 0 4px 20px rgba(109, 40, 217, 0.05);
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        text-align: left;
        border-collapse: collapse;
    }

    .data-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }

    .data-table tbody tr:hover {
        background: #fdfcff;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Popular menu bar */
    .popularity-bar {
        height: 8px;
        border-radius: 99px;
        background: linear-gradient(135deg, #8b5cf6, #c026d3);
        transition: width 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="animate-in space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Ringkasan</h2>
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()" class="bg-white border border-gray-200 text-sm rounded-lg focus:ring-violet-500 focus:border-violet-500 block p-2.5 shadow-sm">
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
            </select>
        </form>
    </div>

    {{-- STATISTIC CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Pendapatan Total --}}
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Total Pendapatan</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="stat-icon bg-emerald-gradient">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-xs font-medium text-emerald-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>
                    @if($period === 'today') Hari Ini
                    @elseif($period === 'month') Bulan Ini
                    @else Semua Waktu
                    @endif
                </span>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Total Transaksi</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                </div>
                <div class="stat-icon bg-violet-gradient">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="text-xs font-medium text-gray-400">
                <span>Total pesanan tercatat
                    @if($period === 'today') (hari ini)
                    @elseif($period === 'month') (bulan ini)
                    @endif
                </span>
            </div>
        </div>

        {{-- Rata-rata Transaksi --}}
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Rata-rata Transaksi</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</h3>
                </div>
                <div class="stat-icon bg-amber-gradient">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            <div class="text-xs font-medium text-gray-400">
                <span>Per transaksi</span>
            </div>
        </div>

        {{-- Best Seller --}}
        <div class="stat-card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500 mb-1">Menu Terlaris</p>
                    <h3 class="text-xl font-black text-gray-900 truncate">{{ $bestSeller && $bestSeller->menu ? $bestSeller->menu->name : '-' }}</h3>
                </div>
                <div class="stat-icon bg-blue-gradient">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
            <div class="text-xs font-medium text-gray-400">
                <span>{{ $bestSeller ? number_format($bestSeller->total_qty) . ' porsi terjual' : 'Belum ada data' }}</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- RECENT TRANSACTIONS TABLE --}}
        <div class="table-container lg:col-span-2">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-900">Transaksi Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>No. Struk</th>
                            <th>Kasir</th>
                            <th>Metode</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>
                                <div class="text-gray-900 font-bold">{{ $trx->created_at->format('H:i') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $trx->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="font-bold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-md text-xs border border-violet-100">
                                    {{ $trx->receipt_number }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($trx->user->name ?? 'K', 0, 1) }}
                                    </div>
                                    <span class="text-gray-700 font-semibold text-sm">{{ $trx->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($trx->payment_method === 'qris')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    QRIS
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    TUNAI
                                </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="font-black text-gray-900">Rp {{ number_format($trx->total, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-400 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada transaksi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>

        {{-- MENU POPULARITY --}}
        <div class="table-container">
            <div class="p-6 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-gray-900">Menu Populer</h3>
                <p class="text-xs text-gray-500 mt-1">Top 5 menu terlaris</p>
            </div>
            <div class="p-4 space-y-4">
                @forelse($menuPopularity as $item)
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $item->menu->name ?? '-' }}</span>
                        <span class="text-xs font-bold text-violet-600 ml-2 shrink-0">{{ $item->total_qty }} porsi</span>
                    </div>
                    <div class="w-full bg-violet-50 rounded-full overflow-hidden">
                        <div class="popularity-bar" style="width: {{ $maxQty > 0 ? ($item->total_qty / $maxQty) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm text-gray-400">Belum ada data penjualan.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection