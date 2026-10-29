<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gorengan Mama Ilham</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
    $clientKey = env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-XXXXX'); // fallback agar script tidak blank
    // Jika mode production di .env aktif, gunakan script production
    $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    $isProduction = $isProduction === true || $isProduction === 'true';
    $snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        violet: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95'
                        },
                        fuchsia: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75'
                        },
                        indigo: {
                            900: '#1e1b4b',
                            800: '#232060',
                            700: '#312e81',
                            600: '#3730a3'
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706'
                        },
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* ——— GLOBAL ——— */
        * {
            box-sizing: border-box;
        }

        body {
            background: #f8f6fd;
            background-image:
                radial-gradient(ellipse at 10% 20%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 90% 80%, rgba(167, 139, 250, 0.04) 0%, transparent 45%);
        }

        /* ——— SIDEBAR ——— */
        .sidebar {
            background: linear-gradient(180deg, #1a1744 0%, #252260 40%, #1e1b4b 100%);
            border-right: 1px solid rgba(139, 92, 246, 0.08);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: rgba(199, 191, 255, 0.75);
            text-decoration: none;
            transition: all 0.18s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #e9d5ff;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #7c3aed, #c026d3);
            color: white;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.5);
        }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(199, 191, 255, 0.35);
            padding: 0 12px;
            margin-bottom: 8px;
        }

        /* ——— HEADER ——— */
        .top-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(139, 92, 246, 0.12);
        }

        .gradient-title {
            background: linear-gradient(135deg, #6d28d9, #c026d3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ——— CARDS ——— */
        .card {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(139, 92, 246, 0.1);
            box-shadow: 0 2px 16px rgba(109, 40, 217, 0.06);
        }

        .card-hover {
            transition: all 0.22s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(109, 40, 217, 0.12);
            border-color: rgba(139, 92, 246, 0.25);
        }

        /* ——— BUTTONS ——— */
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #c026d3);
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(139, 92, 246, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(139, 92, 246, 0.45);
        }

        .btn-primary:active:not(:disabled) {
            transform: scale(0.97);
        }

        /* ——— SCROLLBAR ——— */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #c4b5fd;
            border-radius: 99px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        /* ——— BADGE ——— */
        .badge-online {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid #6ee7b7;
            font-size: 11px;
            font-weight: 700;
        }

        /* ——— ANIMATIONS ——— */
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeSlideIn 0.35s ease both;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

    @php
    $user = auth()->user();
    $initials = strtoupper(substr(collect(explode(' ', $user->name))->map(fn ($w) => mb_substr($w, 0, 1))->implode(''), 0, 2));
    $roleLabel = $user->role === 'admin' ? 'Administrator' : 'Kasir';
    @endphp

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 z-40 lg:hidden"
        style="background: rgba(30,27,75,0.6); backdrop-filter: blur(4px);"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"></div>

    {{-- ===== SIDEBAR ===== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="sidebar fixed inset-y-0 left-0 z-50 w-64 flex flex-col transform transition-transform duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="px-5 py-6" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shrink-0 relative overflow-hidden"
                    style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 6px 24px rgba(245,158,11,0.5);">
                    <span style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">🍳</span>
                </div>
                <div>
                    <h1 class="font-black text-base leading-tight text-white tracking-tight">Gorengan</h1>
                    <p class="text-xs font-bold" style="color: #fbbf24; letter-spacing: 0.05em;">Mama Ilham</p>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p class="sidebar-section-label mb-3">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('kasir') }}"
                class="sidebar-link {{ request()->routeIs('kasir') ? 'active' : '' }}">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Sistem Kasir
                {{-- Active dot --}}
                @if(request()->routeIs('kasir'))
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                @endif
            </a>

            @if($user->role === 'admin')
            <div class="pt-5 pb-2">
                <p class="sidebar-section-label">Admin Panel</p>
            </div>
            <a href="{{ route('reports.index') }}"
                class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Laporan
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Kelola Pengguna
            </a>
            <a href="{{ route('admin.menus.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                <svg style="width:17px;height:17px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Kelola Menu
            </a>
            @endif
        </nav>

        {{-- Profile + Logout --}}
        <div class="p-3" style="border-top: 1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center gap-3 p-3 rounded-2xl mb-2"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-xs shrink-0"
                    style="background: linear-gradient(135deg, #8b5cf6, #c026d3); box-shadow: 0 4px 14px rgba(139,92,246,0.5);">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-white truncate leading-tight">{{ $user->name }}</p>
                    <p class="text-xs font-semibold" style="color: #a78bfa;">{{ $roleLabel }}</p>
                </div>
                {{-- Online dot --}}
                <span class="w-2 h-2 rounded-full shrink-0" style="background: #4ade80; box-shadow: 0 0 6px #4ade80;"></span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold transition-all duration-200"
                    style="color: rgba(252,165,165,0.8); border: 1px solid rgba(252,165,165,0.15);"
                    onmouseover="this.style.background='rgba(239,68,68,0.12)'; this.style.color='#fca5a5'"
                    onmouseout="this.style.background='transparent'; this.style.color='rgba(252,165,165,0.8)'">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN WRAPPER ===== --}}
    <div class="lg:pl-64 min-h-screen flex flex-col">

        {{-- Top Header --}}
        <header class="top-header sticky top-0 z-30 px-4 py-3 lg:px-6 flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden p-2 rounded-xl text-violet-600 hover:bg-violet-50 transition-colors">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="flex-1 min-w-0">
                <h2 class="gradient-title font-black text-lg leading-tight truncate">@yield('page-title')</h2>
                <p class="text-xs text-gray-400 hidden sm:block mt-0.5 font-medium">@yield('page-subtitle', '')</p>
            </div>

            {{-- Time display --}}
            <div class="hidden md:block text-xs font-bold text-gray-400" id="live-clock"></div>

            <div class="badge-online hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Warung Buka
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>

    @include('partials.transaction-preview')

    <script>
        // Live clock
        function updateClock() {
            const now = new Date();
            const el = document.getElementById('live-clock');
            if (el) el.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>

    @stack('scripts')
</body>

</html> const el = document.getElementById('live-clock');
if (el) {
const now = new Date();
el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
}
updateClock();
setInterval(updateClock, 1000);
</script>

@stack('scripts')
</body>

</html>