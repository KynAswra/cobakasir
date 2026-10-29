{{-- Modal Preview Transaksi — include sekali di layout --}}
<div x-data="transactionPreview()" x-cloak
     @open-transaction-preview.window="open($event.detail.id)">

    {{-- Modal --}}
    <div x-show="show" class="fixed inset-0 z-[70] flex items-center justify-center p-4" style="background: rgba(20,10,40,0.55); backdrop-filter: blur(6px);">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden max-h-[90vh] flex flex-col border border-violet-100"
             style="animation: modalIn 0.28s cubic-bezier(.4,0,.2,1) both;">

            {{-- Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-violet-600 to-violet-700 text-white flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-bold text-lg">Preview Transaksi</h3>
                    <p class="text-violet-200 text-sm" x-text="receipt.receipt_number"></p>
                </div>
                <button @click="close()" class="p-2 rounded-lg hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="p-12 text-center text-gray-400">
                <div class="animate-spin w-8 h-8 border-4 border-violet-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                Memuat...
            </div>

            {{-- Content --}}
            <div x-show="!loading && show" class="overflow-y-auto flex-1 p-6 space-y-4">

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs font-semibold">Tanggal</p>
                        <p class="font-semibold text-gray-800" x-text="receipt.created_at"></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold">Kasir</p>
                        <p class="font-semibold text-gray-800" x-text="receipt.cashier"></p>
                    </div>
                </div>

                {{-- Items --}}
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Item Pesanan</div>
                    <template x-for="item in receipt.items" :key="item.name + item.qty">
                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-50 text-sm">
                            <div>
                                <p class="font-bold text-gray-900" x-text="item.name"></p>
                                <p class="text-xs text-gray-400" x-text="formatRupiah(item.price) + ' × ' + item.qty"></p>
                            </div>
                            <p class="font-extrabold text-gray-900" x-text="formatRupiah(item.subtotal)"></p>
                        </div>
                    </template>
                </div>

                {{-- Payment Info --}}
                <div class="bg-violet-50 rounded-xl p-4 space-y-2 border border-violet-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Metode Bayar</span>
                        <span class="font-bold" x-text="receipt.payment_method === 'tunai' ? '💵 Tunai' : '📱 QRIS'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-600">Total</span>
                        <span class="text-xl font-black text-violet-700" x-text="formatRupiah(receipt.total)"></span>
                    </div>

                    <template x-if="receipt.payment_method === 'tunai'">
                        <div class="border-t border-violet-200 pt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Uang Diterima</span>
                                <span class="font-bold text-gray-900" x-text="formatRupiah(receipt.cash_received)"></span>
                            </div>
                            <div class="flex justify-between items-center bg-emerald-100 rounded-lg px-3 py-2">
                                <span class="font-semibold text-emerald-800">Kembalian</span>
                                <span class="text-lg font-black text-emerald-700" x-text="formatRupiah(receipt.change_amount)"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="receipt.payment_method === 'qris'">
                        <div class="border-t border-violet-200 pt-3">
                            <p class="text-xs text-gray-400 text-center">Pembayaran via QRIS — lunas sesuai total</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div x-show="!loading && show" class="px-6 py-4 border-t border-gray-100 flex gap-3 shrink-0 bg-gray-50">
                <button @click="printReceipt()"
                        class="flex-1 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 font-bold text-sm hover:bg-white flex items-center justify-center gap-2 transition-colors">
                    🖨️ Cetak Struk
                </button>
                <button @click="close()"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm hover:bg-violet-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Print area --}}
    <div id="preview-print-receipt" style="display:none;">
        <div style="font-family: 'Courier New', monospace; font-size: 12px; color: #000; max-width: 80mm; padding: 5mm; line-height: 1.5;">

            {{-- Header --}}
            <div style="text-align: center; padding-bottom: 6px;">
                <div style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">GORENGAN</div>
                <div style="font-size: 14px; font-weight: bold;">MAMA ILHAM</div>
                <div style="font-size: 10px; margin-top: 4px; color: #333;">Warung Gorengan Terbaik</div>
            </div>

            <div style="border-top: 2px double #000; margin: 8px 0;"></div>

            {{-- Info --}}
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

            <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

            {{-- Items --}}
            <div style="font-size: 11px;">
                <template x-for="item in receipt.items" :key="'p'+item.name">
                    <div style="padding: 4px 0;">
                        <div style="font-weight: bold;" x-text="item.name"></div>
                        <div style="display: flex; justify-content: space-between; padding-left: 10px; color: #333;">
                            <span x-text="item.qty + ' x ' + formatRupiah(item.price || (item.subtotal / item.qty))"></span>
                            <span style="font-weight: bold;" x-text="formatRupiah(item.subtotal)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div style="border-top: 2px double #000; margin: 8px 0;"></div>

            {{-- Totals --}}
            <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 3px 0; font-weight: bold; font-size: 14px;">TOTAL</td>
                    <td style="padding: 3px 0; font-weight: bold; font-size: 14px; text-align: right;" x-text="formatRupiah(receipt.total)"></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0; font-size: 11px;">Bayar</td>
                    <td style="padding: 2px 0; font-size: 11px; text-align: right;" x-text="receipt.payment_method === 'tunai' ? formatRupiah(receipt.cash_received) : formatRupiah(receipt.total)"></td>
                </tr>
                <template x-if="receipt.payment_method === 'tunai'">
                    <tr>
                        <td style="padding: 2px 0; font-size: 11px; font-weight: bold;">Kembalian</td>
                        <td style="padding: 2px 0; font-size: 11px; font-weight: bold; text-align: right;" x-text="formatRupiah(receipt.change_amount)"></td>
                    </tr>
                </template>
            </table>

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

@once
@push('styles')
<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.96) translateY(16px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    @media print {
        body * { visibility: hidden !important; }
        #preview-print-receipt, #preview-print-receipt * { visibility: visible !important; }
        #preview-print-receipt {
            display: block !important;
            position: absolute;
            left: 0; top: 0;
            width: 80mm;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function transactionPreview() {
        return {
            show: false,
            loading: false,
            receipt: { receipt_number: '', total: 0, payment_method: '', cash_received: 0, change_amount: 0, created_at: '', cashier: '', items: [] },

            formatRupiah(amount) {
                return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
            },

            async open(id) {
                this.show = true;
                this.loading = true;
                try {
                    const res = await fetch(`/transactions/${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Gagal memuat transaksi.');
                    this.receipt = await res.json();
                } catch (e) {
                    alert(e.message);
                    this.close();
                } finally {
                    this.loading = false;
                }
            },

            close() {
                this.show = false;
            },

            printReceipt() {
                const el = document.getElementById('preview-print-receipt');
                const prev = el.style.display;
                el.style.display = 'block';
                window.print();
                el.style.display = prev;
            },
        }
    }
</script>
@endpush
@endonce
