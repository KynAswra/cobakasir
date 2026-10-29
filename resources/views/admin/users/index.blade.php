@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')
@section('page-subtitle', 'Tambah akun baru atau ubah password kasir & admin')

@section('content')
<div x-data="{ passwordModal: null, addUserModal: false }" class="space-y-6">

    @include('partials.flash')

    {{-- Header dengan tombol tambah --}}
    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <h3 class="font-bold text-stone-900">Daftar Pengguna</h3>
            <button @click="addUserModal = true"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-sm shadow-md shadow-brand-500/30 hover:from-brand-600 hover:to-brand-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pengguna
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-left">
                        <th class="px-6 py-3 font-semibold">Nama</th>
                        <th class="px-6 py-3 font-semibold">Email</th>
                        <th class="px-6 py-3 font-semibold">Role</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-brand-50/30 transition-colors">
                        <td class="px-6 py-4 font-semibold text-stone-900">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="ml-1 text-xs text-brand-600">(Anda)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-stone-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role === 'admin' ? 'Admin' : 'Kasir' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="passwordModal = {{ $user->id }}"
                                        class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold hover:bg-amber-100 transition-colors">
                                    Ubah Password
                                </button>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal tambah pengguna --}}
    <div x-show="addUserModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.outside="addUserModal = false"
             class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #8b5cf6, #c026d3); box-shadow: 0 4px 16px rgba(139,92,246,0.4);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-stone-900">Tambah Pengguna Baru</h3>
                    <p class="text-xs text-stone-400">Isi data pengguna di bawah ini</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4" autocomplete="off">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Masukkan nama lengkap" autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="Masukkan alamat email" autocomplete="new-email"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Password</label>
                    <input type="password" name="password" required minlength="6"
                           placeholder="Minimal 6 karakter" autocomplete="new-password"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Role</label>
                    <select name="role" required
                            class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 outline-none text-sm font-medium bg-white">
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="addUserModal = false"
                            class="flex-1 py-2.5 rounded-xl border-2 border-stone-200 text-stone-600 font-semibold text-sm hover:bg-stone-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-sm shadow-md shadow-brand-500/30 hover:from-brand-600 hover:to-brand-700 transition-all">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal ubah password --}}
    @foreach($users as $user)
    <div x-show="passwordModal === {{ $user->id }}" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.outside="passwordModal = null"
             class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="font-bold text-lg text-stone-900 mb-1">Ubah Password</h3>
            <p class="text-sm text-stone-500 mb-5">{{ $user->name }} — {{ $user->email }}</p>
            <form method="POST" action="{{ route('admin.users.password', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="6"
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-500 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required minlength="6"
                           placeholder="Ulangi password baru"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-stone-200 focus:border-brand-500 outline-none text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="passwordModal = null"
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
