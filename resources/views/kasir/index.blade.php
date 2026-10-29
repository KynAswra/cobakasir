@extends('layouts.app')

@section('title', 'Sistem Kasir')
@section('page-title', 'Sistem Kasir')
@section('page-subtitle', 'Point of Sale — Gorengan Mama Ilham')

@push('styles')
<style>
    /* ——— PRINT ——— */
    @media print {
        body * { visibility: hidden !important; }
        #print-receipt, #print-receipt * { visibility: visible !important; }
        #print-receipt {
            display: block !important;
            position: absolute; left: 0; top: 0;
            width: 80mm; padding: 5mm;
            font-family: 'Courier New', monospace !important;
        }
    }
    #print-receipt { display: none; }

    /* ——— MENU CARDS ——— */
    .menu-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8e0f3;
        box-shadow: 0 2px 8px rgba(99,66,163,0.04);
        transition: all 0.22s cubic-bezier(.4,0,.2,1);
        cursor: pointer; overflow: hidden; text-align: left;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(99,66,163,0.1);
        border-color: #c9b8e8;
    }
    .menu-card:active { transform: scale(0.97); }
    .menu-card .menu-img {
        width: 100%; height: 120px; object-fit: cover;
        border-bottom: 1px solid #f3eff9;
    }
    .menu-card .menu-placeholder {
        width: 100%; height: 120px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, #f5f0ff 0%, #ede5fb 100%);
        border-bottom: 1px solid #ede5fb;
    }

    /* ——— CART ——— */
    .cart-panel {
        background: #fff;
        border-radius: 22px;
        border: 1px solid #e8e0f3;
        box-shadow: 0 4px 24px rgba(99,66,163,0.06);
    }
    .search-input {
        background: #faf8fd;
        border: 2px solid #ede5fb;
        border-radius: 14px;
        padding: 12px 16px 12px 44px;
        font-size: 14px; color: #2d1b4e; outline: none; width: 100%;
        transition: all 0.2s;
        font-weight: 500;
    }
    .search-input:focus { border-color: #a78bfa; box-shadow: 0 0 0 4px rgba(167,139,250,0.12); }
    .search-input::placeholder { color: #b8a4d6; }

    /* ——— QTY BUTTONS ——— */
    .qty-btn {
        background: #f5f0ff; color: #7c3aed;
        border: 1.5px solid #e8e0f3; border-radius: 10px;
        width: 30px; height: 30px; font-weight: 700; font-size: 15px;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s;
        line-height: 1;
    }
    .qty-btn:hover { background: #ede5fb; border-color: #c9b8e8; }
    .qty-btn:active { transform: scale(0.92); }

    /* ——— PAY BUTTONS ——— */
    .pay-cash {
        background: linear-gradient(135deg, #059669, #047857);
        color: white; font-weight: 700; font-size: 14px;
        border: none; border-radius: 14px; cursor: pointer;
        box-shadow: 0 4px 16px rgba(5,150,105,0.25);
        transition: all 0.2s; padding: 15px 16px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .pay-cash:hover:not(:disabled) { box-shadow: 0 8px 24px rgba(5,150,105,0.35); transform: translateY(-1px); }

    .pay-qris {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white; font-weight: 700; font-size: 14px;
        border: none; border-radius: 14px; cursor: pointer;
        box-shadow: 0 4px 16px rgba(124,58,237,0.25);
        transition: all 0.2s; padding: 15px 16px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .pay-qris:hover:not(:disabled) { box-shadow: 0 8px 24px rgba(124,58,237,0.35); transform: translateY(-1px); }

    .pay-cash:disabled, .pay-qris:disabled {
        opacity: 0.5; cursor: not-allowed; transform: none !important; box-shadow: none;
    }

    /* ——— MODALS ——— */
    .modal-overlay { background: rgba(20,10,40,0.55); backdrop-filter: blur(6px); }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.96) translateY(16px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-card { animation: modalIn 0.28s cubic-bezier(.4,0,.2,1) both; }
</style>
@endpush

@section('content')
<div x-data="posSystem()" class="h-[calc(100vh-7rem)] lg:h-[calc(100vh-5.5rem)] animate-in">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 h-full">

        {{-- ========== LEFT: MENU GRID ========== --}}
        <div class="lg:col-span-8 flex flex-col min-h-0 gap-4">

            {{-- Search Bar --}}
            <div class="relative shrink-0">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-violet-400 w-5 h-5 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="Cari gorengan..." class="search-input">
            </div>

            {{-- Menu Grid --}}
            <div class="flex-1 overflow-y-auto pr-1 pb-2">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-if="filteredMenu.length === 0">
                        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center text-violet-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-400">Menu tidak ditemukan</p>
                        </div>
                    </template>
                    <template x-for="item in filteredMenu" :key="item.id">
                        <button @click="addToCart(item)" class="menu-card flex flex-col">
                            <template x-if="item.image_url">
                                <img :src="item.image_url" :alt="item.name" class="menu-img">
                            </template>
                            <template x-if="!item.image_url">
                                <div class="menu-placeholder">
                                    <svg class="w-8 h-8 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                            <div class="p-3 mt-auto">
                                <h4 class="font-bold text-gray-800 text-sm leading-snug mb-1 line-clamp-2" x-text="item.name"></h4>
                                <p class="font-extrabold text-violet-600 text-sm" x-text="formatRupiah(item.price)"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ========== RIGHT: CART ========== --}}
        <div class="lg:col-span-4 flex flex-col min-h-0">
            <div class="cart-panel flex-1 min-h-0 flex flex-col">

                {{-- Cart Header --}}
                <div class="shrink-0 flex items-center justify-between px-5 py-4 border-b border-violet-50">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-base tracking-tight">🛒 Pesanan</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5"
                           x-text="cart.length > 0 ? totalItems + ' item' : 'Belum ada item'"></p>
                    </div>
                    <button x-show="cart.length > 0" @click="clearCart()"
                            class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                        Hapus Semua
                    </button>
                </div>

                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto px-3 py-2">
                    <template x-if="cart.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center px-6">
                            <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center text-violet-200 mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400">Klik menu untuk<br>menambahkan pesanan</p>
                        </div>
                    </template>
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-violet-50/40 transition-colors border-b border-gray-50 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-800 text-sm truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="formatRupiah(item.price) + ' /pcs'"></p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button @click="decreaseQty(index)" class="qty-btn">−</button>
                                <span class="w-7 text-center font-extrabold text-gray-800 text-sm" x-text="item.qty"></span>
                                <button @click="increaseQty(index)" class="qty-btn">+</button>
                            </div>
                            <div class="w-[76px] text-right shrink-0">
                                <p class="font-extrabold text-violet-700 text-sm" x-text="formatRupiah(item.price * item.qty)"></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Cart Footer --}}
                <div class="shrink-0 px-5 py-5 bg-gradient-to-t from-violet-50/80 to-white border-t border-violet-100/60">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total</span>
                        <span class="text-2xl font-black text-gray-900 tracking-tight" x-text="formatRupiah(totalPrice)"></span>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="errorMessage" x-cloak
                         class="mb-3 px-3 py-2.5 rounded-xl text-xs font-semibold bg-red-50 text-red-600 border border-red-100 flex items-start gap-2">
                         <span class="shrink-0 mt-0.5">⚠️</span>
                         <span x-text="errorMessage"></span>
                    </div>

                    {{-- Pay Buttons --}}
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="openCashModal()" :disabled="cart.length === 0 || loading" class="pay-cash">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Tunai
                        </button>
                        <button @click="pay('qris')" :disabled="cart.length === 0 || loading" class="pay-qris">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span x-text="loading ? 'Proses...' : 'QRIS'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL: BAYAR TUNAI ========== --}}
    <div x-show="showCashModal" x-cloak class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-4 modal-overlay">
        <div @click.outside="showCashModal = false" class="modal-card bg-white w-full max-w-md rounded-2xl shadow-2xl border border-violet-100">
            <div class="p-6 sm:p-8">
                <h3 class="font-extrabold text-gray-900 text-xl mb-1">Pembayaran Tunai</h3>
                <p class="text-sm text-gray-400 mb-6">Masukkan nominal uang dari pelanggan</p>

                <div class="flex justify-between items-center mb-6 p-4 bg-violet-50 rounded-xl border border-violet-100">
                    <span class="text-sm font-bold text-violet-600 uppercase tracking-wide">Tagihan</span>
                    <span class="text-2xl font-black text-gray-900" x-text="formatRupiah(totalPrice)"></span>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Uang Diterima</label>
                    <input type="number" x-model.number="cashReceived" min="0" step="500"
                           @keydown.enter="confirmCashPayment()" placeholder="0"
                           class="w-full px-4 py-4 rounded-xl text-xl font-black text-gray-900 outline-none border-2 border-gray-200 focus:border-violet-500 focus:ring-4 focus:ring-violet-100 transition-all text-right">
                </div>

                <div class="grid grid-cols-4 gap-2 mb-4">
                    <template x-for="nominal in quickCash" :key="nominal">
                        <button @click="addQuickCash(nominal)"
                                class="py-2.5 rounded-xl text-xs font-bold border-2 border-gray-100 text-gray-600 hover:bg-violet-50 hover:text-violet-700 hover:border-violet-200 transition-all"
                                x-text="formatRupiahShort(nominal)"></button>
                    </template>
                    <button @click="cashReceived = totalPrice"
                            class="col-span-4 py-2.5 rounded-xl text-sm font-bold bg-violet-100 text-violet-700 hover:bg-violet-200 transition-colors">
                        💰 Uang Pas
                    </button>
                </div>

                <div class="p-4 mb-6 rounded-xl border-2 transition-colors"
                     :class="changeAmount >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-sm" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-600'">Kembalian</span>
                        <span class="text-xl font-black" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-600'"
                              x-text="changeAmount >= 0 ? formatRupiah(changeAmount) : '− ' + formatRupiah(Math.abs(changeAmount))"></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showCashModal = false"
                            class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 border-2 border-gray-200 hover:bg-gray-50 transition-colors">Batal</button>
                    <button @click="confirmCashPayment()" :disabled="changeAmount < 0 || loading || !cashReceived"
                            class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-lg shadow-emerald-600/25">
                        <span x-text="loading ? 'Memproses...' : '✓ Bayar'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL: SUKSES ========== --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 modal-overlay">
        <div class="modal-card bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-violet-100 overflow-hidden">

            {{-- Success Header --}}
            <div class="px-8 pt-8 pb-4 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-1">Transaksi Berhasil!</h3>
                <p class="text-sm text-gray-400 font-semibold" x-text="'Struk #' + (receipt.receipt_number || '')"></p>
            </div>

            {{-- Receipt Details --}}
            <div class="mx-6 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total</span>
                    <span class="font-black text-gray-900" x-text="formatRupiah(receipt.total)"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-bold px-2 py-0.5 rounded-md text-xs"
                          :class="receipt.payment_method === 'tunai' ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700'"
                          x-text="receipt.payment_method === 'tunai' ? 'TUNAI' : 'QRIS'"></span>
                </div>
                <template x-if="receipt.payment_method === 'tunai'">
                    <div class="pt-2 mt-2 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibayar</span>
                            <span class="font-bold text-gray-900" x-text="formatRupiah(receipt.cash_received)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kembalian</span>
                            <span class="font-bold text-emerald-600" x-text="formatRupiah(receipt.change_amount)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Actions --}}
            <div class="px-6 pb-6 space-y-2.5">
                <button @click="newTransaction()"
                        class="w-full py-3.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 transition-all shadow-lg shadow-violet-600/25">
                    Transaksi Baru →
                </button>
                <button @click="printReceipt()"
                        class="w-full py-3 rounded-xl font-bold text-sm text-gray-500 border-2 border-gray-200 hover:bg-gray-50 transition-colors">
                    🖨️ Cetak Struk
                </button>
            </div>
        </div>
    </div>

    {{-- ========== PRINT RECEIPT (Thermal 80mm) ========== --}}
    <div id="print-receipt">
        <div style="font-family: 'Courier New', monospace; font-size: 12px; color: #000; max-width: 80mm; line-height: 1.5; padding: 2mm 0;">

            {{-- Header --}}
            <div style="text-align: center; padding-bottom: 6px;">
                <div style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">GORENGAN</div>
                <div style="font-size: 14px; font-weight: bold;">MAMA ILHAM</div>
                <div style="font-size: 10px; margin-top: 4px; color: #333;">Warung Gorengan Terbaik</div>
            </div>

            {{-- Separator double --}}
            <div style="border-top: 2px double #000; margin: 8px 0;"></div>

            {{-- Info Transaksi --}}
            <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 2px 0; width: 35%;">No. Struk</td>
                    <td style="padding: 2px 0; width: 5%;">:</td>
                    <td style="padding: 2px 0; font-weight: bold; text-align: right;" x-text="receipt.receipt_number"></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0;">Tanggal</td>
                    <td style="padding: 2px 0;">:</td>
                    <td style="padding: 2px 0; text-align: right;" x-text="receipt.created_at"></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0;">Kasir</td>
                    <td style="padding: 2px 0;">:</td>
                    <td style="padding: 2px 0; text-align: right;" x-text="receipt.cashier"></td>
                </tr>
            </table>

            {{-- Separator dashed --}}
            <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

            {{-- Items --}}
            <div style="font-size: 11px;">
                <template x-for="item in (receipt.items || [])" :key="item.name">
                    <div style="padding: 4px 0;">
                        <div style="font-weight: bold;" x-text="item.name"></div>
                        <div style="display: flex; justify-content: space-between; padding-left: 10px; color: #333;">
                            <span x-text="item.qty + ' x ' + formatRupiah(item.price || (item.subtotal / item.qty))"></span>
                            <span style="font-weight: bold;" x-text="formatRupiah(item.subtotal)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Separator double --}}
            <div style="border-top: 2px double #000; margin: 8px 0;"></div>

            {{-- Totals --}}
            <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 3px 0; font-weight: bold; font-size: 14px;">TOTAL</td>
                    <td style="padding: 3px 0; font-weight: bold; font-size: 14px; text-align: right;" x-text="formatRupiah(receipt.total)"></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0; font-size: 11px;" x-text="'Bayar (' + (receipt.payment_method === 'tunai' ? 'Tunai' : 'QRIS') + ')'"></td>
                    <td style="padding: 2px 0; font-size: 11px; text-align: right;" x-text="receipt.payment_method === 'tunai' ? formatRupiah(receipt.cash_received) : formatRupiah(receipt.total)"></td>
                </tr>
                <template x-if="receipt.payment_method === 'tunai'">
                    <tr>
                        <td style="padding: 2px 0; font-size: 11px; font-weight: bold;">Kembalian</td>
                        <td style="padding: 2px 0; font-size: 11px; font-weight: bold; text-align: right;" x-text="formatRupiah(receipt.change_amount)"></td>
                    </tr>
                </template>
            </table>

            {{-- Separator dashed --}}
            <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

            {{-- Footer --}}
            <div style="text-align: center; font-size: 11px; padding: 4px 0; line-height: 1.6;">
                <div style="font-weight: bold;">Terima kasih!</div>
                <div>Selamat menikmati 🍳</div>
                <div style="font-size: 9px; margin-top: 6px; color: #555;">— Gorengan Mama Ilham —</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posSystem() {
    return {
        menu: @json($menus ?? []),
        cart: [], search: '', loading: false, errorMessage: '',
        showCashModal: false, showModal: false, cashReceived: 0, receipt: {},
        quickCash: [2000, 5000, 10000, 20000, 50000, 100000],

        get filteredMenu() {
            if (!this.search) return this.menu;
            const q = this.search.toLowerCase();
            return this.menu.filter(m => m.name.toLowerCase().includes(q));
        },
        get totalItems() { return this.cart.reduce((s, i) => s + i.qty, 0); },
        get totalPrice() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
        get changeAmount() { return (Number(this.cashReceived) || 0) - this.totalPrice; },

        addToCart(item) {
            const found = this.cart.find(i => i.id === item.id);
            if (found) found.qty++;
            else this.cart.push({ ...item, qty: 1 });
            this.errorMessage = '';
        },
        increaseQty(index) { this.cart[index].qty++; },
        decreaseQty(index) {
            if (this.cart[index].qty <= 1) this.cart.splice(index, 1);
            else this.cart[index].qty--;
        },
        clearCart() { this.cart = []; this.errorMessage = ''; },
        openCashModal() {
            if (this.cart.length === 0) return;
            this.cashReceived = 0; this.showCashModal = true;
        },
        addQuickCash(n) { this.cashReceived = (Number(this.cashReceived) || 0) + n; },
        confirmCashPayment() {
            if (this.changeAmount < 0 || !this.cashReceived) return;
            this.pay('tunai', this.cashReceived);
        },

        async pay(method, cashReceived = null) {
            if (this.cart.length === 0 || this.loading) return;
            this.loading = true; this.errorMessage = '';

            try {
                if (method === 'qris') {
                    if (typeof window.snap === 'undefined') {
                        throw new Error('Midtrans belum siap. Periksa koneksi internet & MIDTRANS_CLIENT_KEY di .env.');
                    }

                    const r = await fetch('{{ route("payment.snap-token") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ items: this.cart.map(i => ({ menu_id: i.id, qty: i.qty })) }),
                    });
                    const d = await r.json();

                    if (!r.ok) {
                        let detail = d.error ? ' (' + (d.error.error_messages ? d.error.error_messages.join(', ') : JSON.stringify(d.error)) + ')' : '';
                        throw new Error((d.message || 'Gagal membuat transaksi.') + detail);
                    }

                    window.snap.pay(d.snap_token, {
                        onSuccess: async () => { this.loading = true; try { await this.saveTransaction('qris', null); } catch(e) { this.errorMessage = e.message; this.loading = false; } },
                        onPending: () => { this.errorMessage = 'Pembayaran QRIS tertunda.'; this.loading = false; },
                        onError: () => { this.errorMessage = 'Pembayaran QRIS gagal.'; this.loading = false; },
                        onClose: () => { this.errorMessage = 'Popup ditutup sebelum selesai.'; this.loading = false; }
                    });
                    return;
                }

                await this.saveTransaction('tunai', cashReceived);
            } catch(e) {
                this.errorMessage = e.message || 'Terjadi kesalahan.';
                this.loading = false;
            }
        },

        async saveTransaction(method, cashReceived) {
            const body = { payment_method: method, items: this.cart.map(i => ({ menu_id: i.id, qty: i.qty })) };
            if (method === 'tunai') body.cash_received = cashReceived;
            const r = await fetch('{{ route("transactions.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(body),
            });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || (d.errors && Object.values(d.errors).flat()[0]) || 'Gagal menyimpan transaksi.');
            this.receipt = { receipt_number: d.receipt_number, total: d.total, payment_method: d.payment_method, cash_received: d.cash_received, change_amount: d.change_amount, created_at: d.created_at, cashier: d.cashier, items: d.items };
            this.showCashModal = false;
            this.showModal = true;
            this.loading = false;
        },

        printReceipt() { window.print(); },
        newTransaction() {
            this.showModal = false; this.clearCart();
            this.cashReceived = 0; this.loading = false; this.errorMessage = '';
        },
        formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
        formatRupiahShort(n) {
            if (n >= 1000000) return (n/1000000) + 'jt';
            if (n >= 1000) return (n/1000) + 'rb';
            return n.toString();
        },
    }
}
</script>
@endpush