@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('page-subtitle', 'Export laporan ke Google Sheets, CSV, atau PDF')

@section('content')
<div class="space-y-6">

    @include('partials.flash')

    {{-- Filter & Export --}}
    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm p-6">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col lg:flex-row lg:items-end gap-4">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm font-medium">
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-stone-100 text-stone-700 font-bold text-sm hover:bg-stone-200 transition-colors">
                    Tampilkan
                </button>
                <a href="{{ route('reports.export.csv', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-stone-200 text-stone-700 font-bold text-sm hover:bg-stone-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download CSV
                </a>
                <button type="button" id="btn-export-pdf"
                    onclick="exportToPdf()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-bold text-sm shadow-md shadow-red-500/25 hover:from-red-600 hover:to-rose-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6" />
                    </svg>
                    Export PDF
                </button>
                <a href="{{ route('reports.export.sheets', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-sm shadow-md shadow-green-500/25 hover:from-green-600 hover:to-green-700 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.5 3.5H8.5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2v-14c0-1.1-.9-2-2-2zm-1 16H9.5v-14h9v14z" />
                        <path d="M11 7h6v2h-6zm0 4h6v2h-6zm0 4h4v2h-4z" />
                    </svg>
                    Export ke Google Sheets
                </a>

                @if(config('google-sheets.enabled') && config('google-sheets.spreadsheet_id'))
                <a href="https://docs.google.com/spreadsheets/d/{{ config('google-sheets.spreadsheet_id') }}/edit"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-green-500 text-green-600 font-bold text-sm hover:bg-green-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Buka Spreadsheet
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-100 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 mb-1">Total Pendapatan</p>
            <p class="text-2xl font-extrabold text-stone-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-stone-100 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 mb-1">Total Transaksi</p>
            <p class="text-2xl font-extrabold text-stone-900">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-stone-100 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 mb-1">Rata-rata Transaksi</p>
            <p class="text-2xl font-extrabold text-stone-900">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-stone-100 shadow-sm">
            <p class="text-xs font-semibold text-stone-500 mb-1">Menu Terlaris</p>
            <p class="text-lg font-extrabold text-stone-900 truncate">{{ $bestSeller?->menu?->name ?? '—' }}</p>
            @if($bestSeller)
            <p class="text-xs text-stone-400">{{ $bestSeller->total_qty }} porsi</p>
            @endif
        </div>
    </div>

    {{-- Tabel transaksi --}}
    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h3 class="font-bold text-stone-900">Detail Transaksi</h3>
            <p class="text-xs text-stone-400 mt-0.5">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</p>
        </div>
        <div class="overflow-x-auto">
            @if($transactions->isEmpty())
            <div class="px-6 py-12 text-center text-stone-400">
                <p class="font-semibold">Tidak ada transaksi pada periode ini</p>
            </div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-left">
                        <th class="px-6 py-3 font-semibold">No. Struk</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold">Kasir</th>
                        <th class="px-6 py-3 font-semibold">Item</th>
                        <th class="px-6 py-3 font-semibold">Bayar</th>
                        <th class="px-6 py-3 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-brand-50/30">
                        <td class="px-6 py-3 font-bold text-brand-600">{{ $trx->receipt_number }}</td>
                        <td class="px-6 py-3 text-stone-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-stone-700">{{ $trx->user->name }}</td>
                        <td class="px-6 py-3 text-stone-700 max-w-[220px] truncate">
                            {{ $trx->items->map(fn($i) => $i->menu->name . ' x' . $i->qty)->join(', ') }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs font-bold {{ $trx->payment_method === 'qris' ? 'text-purple-600' : 'text-green-600' }}">
                                {{ $trx->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right font-bold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-brand-50 font-bold">
                        <td colspan="5" class="px-6 py-3 text-stone-700">Total</td>
                        <td class="px-6 py-3 text-right text-brand-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>
    </div>

    {{-- Popularitas --}}
    @if($menuPopularity->isNotEmpty())
    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm p-6">
        <h3 class="font-bold text-stone-900 mb-4">Popularitas Menu</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-left">
                        <th class="px-4 py-2 font-semibold">Menu</th>
                        <th class="px-4 py-2 font-semibold text-right">Terjual</th>
                        <th class="px-4 py-2 font-semibold text-right">Omset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($menuPopularity as $item)
                    <tr>
                        <td class="px-4 py-2 font-semibold">{{ $item->menu->name }}</td>
                        <td class="px-4 py-2 text-right">{{ $item->total_qty }} porsi</td>
                        <td class="px-4 py-2 text-right font-bold text-brand-600">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Hidden data for PDF --}}
<script id="report-data" type="application/json">
    {
        "dateFrom": "{{ $from->format('d/m/Y') }}",
        "dateTo": "{{ $to->format('d/m/Y') }}",
        "totalRevenue": "Rp {{ number_format($totalRevenue, 0, ',', '.') }}",
        "totalTransactions": "{{ $totalTransactions }}",
        "avgTransaction": "Rp {{ number_format($avgTransaction, 0, ',', '.') }}",
        "bestSeller": "{{ $bestSeller?->menu?->name ?? '-' }}",
        "bestSellerQty": "{{ $bestSeller?->total_qty ?? '' }}",
        "transactions": [
            @foreach($transactions as $trx){
                "receipt": "{{ $trx->receipt_number }}",
                "date": "{{ $trx->created_at->format('d/m/Y H:i') }}",
                "kasir": "{{ addslashes($trx->user->name) }}",
                "items": "{{ addslashes($trx->items->map(fn($i) => $i->menu->name . ' x' . $i->qty)->join(', ')) }}",
                "payment": "{{ $trx->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}",
                "total": "Rp {{ number_format($trx->total, 0, ',', '.') }}"
            }@unless($loop->last),@endunless
            @endforeach
        ],
        "menuPopularity": [
            @foreach($menuPopularity as $item){
                "name": "{{ addslashes($item->menu->name) }}",
                "qty": "{{ $item->total_qty }}",
                "sales": "Rp {{ number_format($item->total_sales, 0, ',', '.') }}"
            }@unless($loop->last),@endunless
            @endforeach
        ]
    }
</script>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    function exportToPdf() {
        const btn = document.getElementById('btn-export-pdf');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Membuat PDF...';
        btn.disabled = true;

        try {
            const rawData = JSON.parse(document.getElementById('report-data').textContent);
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const primaryColor = [234, 88, 12]; // orange-600
            const darkColor = [28, 25, 23]; // stone-900
            const grayColor = [168, 162, 158]; // stone-400
            const lightGray = [245, 245, 244]; // stone-100

            // ── Header ──
            doc.setFillColor(...primaryColor);
            doc.rect(0, 0, 210, 28, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(18);
            doc.setTextColor(255, 255, 255);
            doc.text('Laporan Penjualan', 14, 13);
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.text('Periode: ' + rawData.dateFrom + ' s/d ' + rawData.dateTo, 14, 21);
            doc.text('Dicetak: ' + new Date().toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }), 210 - 14, 21, {
                align: 'right'
            });

            // ── Summary cards ──
            let y = 36;
            const cards = [{
                    label: 'Total Pendapatan',
                    value: rawData.totalRevenue
                },
                {
                    label: 'Total Transaksi',
                    value: rawData.totalTransactions
                },
                {
                    label: 'Rata-rata Transaksi',
                    value: rawData.avgTransaction
                },
                {
                    label: 'Menu Terlaris',
                    value: rawData.bestSeller + (rawData.bestSellerQty ? ' (' + rawData.bestSellerQty + ' porsi)' : '')
                },
            ];
            const cardW = (210 - 28 - 6 * 3) / 4;
            cards.forEach((card, i) => {
                const x = 14 + i * (cardW + 6);
                doc.setFillColor(...lightGray);
                doc.roundedRect(x, y, cardW, 18, 2, 2, 'F');
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.setTextColor(...grayColor);
                doc.text(card.label.toUpperCase(), x + 3, y + 6);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(8.5);
                doc.setTextColor(...darkColor);
                const lines = doc.splitTextToSize(card.value, cardW - 6);
                doc.text(lines[0], x + 3, y + 13);
            });

            // ── Transactions table ──
            y += 26;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(11);
            doc.setTextColor(...darkColor);
            doc.text('Detail Transaksi', 14, y);
            y += 4;

            const trxRows = rawData.transactions.map(t => [
                t.receipt, t.date, t.kasir, t.items, t.payment, t.total
            ]);

            doc.autoTable({
                startY: y,
                head: [
                    ['No. Struk', 'Tanggal', 'Kasir', 'Item', 'Bayar', 'Total']
                ],
                body: trxRows,
                foot: trxRows.length ? [
                    ['', '', '', '', 'TOTAL', rawData.totalRevenue]
                ] : [],
                styles: {
                    fontSize: 7.5,
                    cellPadding: 2.5,
                    textColor: darkColor
                },
                headStyles: {
                    fillColor: primaryColor,
                    textColor: [255, 255, 255],
                    fontStyle: 'bold'
                },
                footStyles: {
                    fillColor: [254, 242, 232],
                    textColor: primaryColor,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: {
                        cellWidth: 24
                    },
                    1: {
                        cellWidth: 24
                    },
                    2: {
                        cellWidth: 22
                    },
                    3: {
                        cellWidth: 'auto'
                    },
                    4: {
                        cellWidth: 16
                    },
                    5: {
                        cellWidth: 28,
                        halign: 'right'
                    },
                },
                alternateRowStyles: {
                    fillColor: [250, 250, 249]
                },
                margin: {
                    left: 14,
                    right: 14
                },
            });

            // ── Menu Popularity table ──
            if (rawData.menuPopularity.length > 0) {
                const afterTrx = doc.lastAutoTable.finalY + 8;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.setTextColor(...darkColor);
                doc.text('Popularitas Menu', 14, afterTrx);

                const popRows = rawData.menuPopularity.map(m => [m.name, m.qty + ' porsi', m.sales]);
                doc.autoTable({
                    startY: afterTrx + 4,
                    head: [
                        ['Menu', 'Terjual', 'Omset']
                    ],
                    body: popRows,
                    styles: {
                        fontSize: 8,
                        cellPadding: 2.5,
                        textColor: darkColor
                    },
                    headStyles: {
                        fillColor: primaryColor,
                        textColor: [255, 255, 255],
                        fontStyle: 'bold'
                    },
                    columnStyles: {
                        0: {
                            cellWidth: 'auto'
                        },
                        1: {
                            cellWidth: 30,
                            halign: 'center'
                        },
                        2: {
                            cellWidth: 40,
                            halign: 'right'
                        },
                    },
                    alternateRowStyles: {
                        fillColor: [250, 250, 249]
                    },
                    margin: {
                        left: 14,
                        right: 14
                    },
                });
            }

            // ── Footer on each page ──
            const pageCount = doc.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.setTextColor(...grayColor);
                doc.text('Halaman ' + i + ' dari ' + pageCount, 210 - 14, 292, {
                    align: 'right'
                });
                doc.text('© Laporan Gorengan Kasir', 14, 292);
            }

            const filename = 'laporan-' + rawData.dateFrom.replace(/\//g, '-') + '_sd_' + rawData.dateTo.replace(/\//g, '-') + '.pdf';
            doc.save(filename);
        } catch (e) {
            console.error(e);
            alert('Gagal membuat PDF. Silakan coba lagi.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>
@endpush
@endsection