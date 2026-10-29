{{-- Toast Notification --}}
@if (session('success') || session('error'))
<div x-data="{ show: true }"
     x-init="setTimeout(() => show = false, 4000)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-[-20px] scale-95"
     class="fixed top-5 right-5 z-[100] max-w-sm w-full"
     style="pointer-events: auto;">
    @if (session('success'))
    <div class="flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl border"
         style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #6ee7b7; box-shadow: 0 20px 60px rgba(5,150,105,0.2);">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="font-bold text-sm text-emerald-900">Berhasil!</p>
            <p class="text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="shrink-0 p-1 rounded-lg text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif
    @if (session('error'))
    <div class="flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl border"
         style="background: linear-gradient(135deg, #fff1f2, #ffe4e6); border-color: #fca5a5; box-shadow: 0 20px 60px rgba(239,68,68,0.2);">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 4px 12px rgba(239,68,68,0.3);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="font-bold text-sm text-red-900">Gagal!</p>
            <p class="text-sm text-red-700 mt-0.5">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="shrink-0 p-1 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif
</div>
@endif

@if ($errors->any())
<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-[-20px] scale-95"
     class="fixed top-5 right-5 z-[100] max-w-sm w-full"
     style="pointer-events: auto;">
    <div class="flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl border"
         style="background: linear-gradient(135deg, #fff7ed, #ffedd5); border-color: #fdba74; box-shadow: 0 20px 60px rgba(234,88,12,0.2);">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #ea580c, #f97316); box-shadow: 0 4px 12px rgba(234,88,12,0.3);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="font-bold text-sm text-orange-900">Validasi Gagal</p>
            <ul class="text-sm text-orange-700 mt-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button @click="show = false" class="shrink-0 p-1 rounded-lg text-orange-400 hover:text-orange-600 hover:bg-orange-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
