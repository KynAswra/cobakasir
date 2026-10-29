@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="login-bg flex items-center justify-center p-4 relative overflow-hidden"
    x-data="{ showPassword: false }">

    {{-- Floating orbs --}}
    <div style="position:absolute; top:10%; left:5%; width:300px; height:300px; border-radius:50%; background:rgba(139,92,246,0.15); filter:blur(80px); pointer-events:none;"></div>
    <div style="position:absolute; bottom:10%; right:5%; width:250px; height:250px; border-radius:50%; background:rgba(192,38,211,0.12); filter:blur(60px); pointer-events:none;"></div>
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:400px; height:400px; border-radius:50%; background:rgba(245,158,11,0.05); filter:blur(100px); pointer-events:none;"></div>

    <div class="w-full max-w-md relative z-10">

        {{-- Brand Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-white tracking-tight leading-tight">Gorengan Mama Ilham</h1>
            <p class="mt-2 font-semibold" style="color: rgba(196,181,253,0.8);">Sistem Kasir Warung Gorengan</p>
        </div>

        {{-- Card --}}
        <div class="login-card p-8">
            <div class="flex flex-col items-center gap-3 mb-6 text-center">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                    style="background: linear-gradient(135deg, #8b5cf6, #c026d3); box-shadow: 0 4px 16px rgba(139,92,246,0.4);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900">Selamat Datang!</h2>
                    <p class="text-sm font-medium mt-1" style="color: #8b5cf6;">Masuk ke akun kasir Anda</p>
                </div>
            </div>

            @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl text-sm font-semibold"
                style="background: #fff1f2; color: #be123c; border: 1.5px solid #fecdd3;">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf


                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: #a78bfa;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required class="input-field" autocomplete="new-email">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: #a78bfa;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" name="password"
                            placeholder="Masukkan password" required class="input-field" style="padding-right:48px;" autocomplete="new-password">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors"
                            style="color: #a78bfa;">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Masuk ke Sistem →
                </button>
            </form>


        </div>
    </div>
</div>
@endsection