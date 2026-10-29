@extends('layouts.app')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu Gorengan')
@section('page-subtitle', 'Tambah, edit, atau nonaktifkan item gorengan dengan foto')

@section('content')
<div x-data="{ editModal: null, addMenuModal: false }" class="space-y-6">

    @include('partials.flash')

    {{-- Daftar menu dengan tombol tambah --}}
    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-stone-900">Daftar Menu</h3>
                <span class="text-xs font-semibold text-stone-400 bg-stone-100 px-2.5 py-0.5 rounded-full">{{ $menus->count() }} item</span>
            </div>
            <button @click="addMenuModal = true"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-sm shadow-md shadow-brand-500/30 hover:from-brand-600 hover:to-brand-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Menu
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-left">
                        <th class="px-6 py-3 font-semibold">Foto</th>
                        <th class="px-6 py-3 font-semibold">Nama Menu</th>
                        <th class="px-6 py-3 font-semibold">Harga</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-brand-50/30 transition-colors {{ !$menu->is_active ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">
                            @if($menu->image)
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}"
                                     class="w-14 h-14 rounded-xl object-cover border border-stone-200">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-stone-900">{{ $menu->name }}</td>
                        <td class="px-6 py-4 font-bold text-brand-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($menu->is_active)
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-200 text-stone-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editModal = {{ $menu->id }}"
                                        class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100 transition-colors">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}"
                                      onsubmit="return confirm('Hapus/nonaktifkan menu {{ $menu->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-stone-400">
                            Belum ada menu. Klik tombol "Tambah Menu" untuk menambahkan item.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal tambah menu --}}
    <div x-show="addMenuModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.outside="addMenuModal = false"
             class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 16px rgba(245,158,11,0.4);">
                    <span class="text-xl">🍳</span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-stone-900">Tambah Item Gorengan</h3>
                    <p class="text-xs text-stone-400">Isi data menu baru di bawah ini</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.menus.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Nama Menu</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Masukkan nama menu"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="500" step="500"
                           placeholder="Masukkan harga"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Foto Menu</label>
                    <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp" required
                           class="w-full px-3 py-2 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold file:text-xs file:cursor-pointer">
                    <p class="text-[10px] text-stone-400 mt-1">JPG, PNG, WEBP — maks. 2MB</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="addMenuModal = false"
                            class="flex-1 py-2.5 rounded-xl border-2 border-stone-200 text-stone-600 font-semibold text-sm hover:bg-stone-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-sm shadow-md shadow-brand-500/30 hover:from-brand-600 hover:to-brand-700 transition-all">
                        Tambah Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal edit menu --}}
    @foreach($menus as $menu)
    <div x-show="editModal === {{ $menu->id }}" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.outside="editModal = null"
             class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg text-stone-900 mb-5">Edit Menu — {{ $menu->name }}</h3>
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Nama Menu</label>
                    <input type="text" name="name" value="{{ $menu->name }}" required
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ $menu->price }}" required min="500" step="500"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Foto Menu</label>
                    @if($menu->image)
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}"
                             class="w-20 h-20 rounded-xl object-cover border border-stone-200 mb-2">
                    @endif
                    <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp"
                           class="w-full px-3 py-2 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold file:text-xs">
                    <p class="text-[10px] text-stone-400 mt-1">Kosongkan jika tidak ingin mengganti foto</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Status</label>
                    <select name="is_active"
                            class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm bg-white">
                        <option value="1" {{ $menu->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$menu->is_active ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="editModal = null"
                            class="flex-1 py-2.5 rounded-xl border-2 border-stone-200 text-stone-600 font-semibold text-sm hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-brand-500 text-white font-bold text-sm hover:bg-brand-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
