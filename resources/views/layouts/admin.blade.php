<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteFavicon = \App\Models\Setting::get('app_favicon');
        $siteLogo = \App\Models\Setting::get('app_logo');
        $siteAppName = \App\Models\Setting::get('app_name', 'E-Voting System');
        $siteOrgName = \App\Models\Setting::get('institution_name', 'Administrator');
    @endphp
    <title>{{ $title ?? 'Admin' }} - {{ $siteAppName }}</title>
    @if ($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteFavicon) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $siteFavicon) }}">
    @endif

    <!-- Tailwind CSS v4 Browser CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/voting.css') }}">
    @stack('styles')

    @livewireStyles
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex overflow-hidden bg-slate-50">

    <!-- Sidebar Backdrop for Mobile -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-20 bg-slate-900/30 lg:hidden hidden"></div>

    <!-- Admin Sidebar (Light & Clean) -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 text-slate-800 flex flex-col transition-transform duration-200 lg:static lg:translate-x-0 -translate-x-full shadow-xs">
        <!-- Brand Header -->
        <div class="h-16 flex items-center px-6 gap-3 border-b border-slate-100">
            @if ($siteLogo)
                <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteAppName }}" class="w-8 h-8 rounded-lg object-contain shadow-2xs">
            @else
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-xs">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-sm text-slate-900 tracking-tight truncate">{{ $siteAppName }}</h1>
                <p class="text-[11px] text-slate-500 font-medium truncate">{{ $siteOrgName }}</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie w-5 text-slate-500"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.elections.index') }}" class="sidebar-link {{ request()->routeIs('admin.elections.*') ? 'active' : '' }}">
                <i class="fa-solid fa-vote-yea w-5 text-slate-500"></i>
                <span>Pemilihan</span>
            </a>

            <a href="{{ route('admin.candidates.index') }}" class="sidebar-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-line w-5 text-slate-500"></i>
                <span>Kandidat</span>
            </a>

            <a href="{{ route('admin.voters.index') }}" class="sidebar-link {{ request()->routeIs('admin.voters.*') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card-clip w-5 text-slate-500"></i>
                <span>Daftar Pemilih</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Monitoring & Log</div>

            <a href="{{ route('admin.live-voting') }}" class="sidebar-link {{ request()->routeIs('admin.live-voting') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-column w-5 text-blue-600"></i>
                <span>Live Voting Monitor</span>
            </a>

            <a href="{{ route('screen.live') }}" target="_blank" class="sidebar-link text-slate-700 hover:text-slate-900">
                <i class="fa-solid fa-desktop w-5 text-amber-600"></i>
                <span>Layar Monitor (Kiosk)</span>
            </a>

            <a href="{{ route('admin.audit-logs') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved w-5 text-slate-500"></i>
                <span>Audit Log</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pengaturan</div>

            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders w-5 text-slate-500"></i>
                <span>Pengaturan Website</span>
            </a>

            <a href="{{ route('admin.profile') }}" class="sidebar-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield w-5 text-slate-500"></i>
                <span>Profil &amp; Keamanan</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pratinjau</div>
            
            <a href="{{ route('voter.dashboard') }}" target="_blank" class="sidebar-link text-slate-600 hover:text-slate-900">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-slate-400"></i>
                <span>Portal Pemilih</span>
            </a>
        </div>

        <!-- Current User Profile Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 overflow-hidden group hover:opacity-80 transition flex-1 mr-2" title="Kelola Profil & Password">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 group-hover:bg-blue-600 group-hover:text-white transition flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-slate-900 group-hover:text-blue-600 transition truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-slate-500">Admin</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-semibold text-slate-900">{{ $title ?? 'Dashboard' }}</span>
                </div>
            </div>


        </header>

        <!-- Main Body Slot -->
        <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
            {{ $slot }}
        </main>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/voting.js') }}"></script>
    <script src="{{ asset('js/live-results.js') }}"></script>
    @stack('scripts')

    @livewireScripts
</body>
</html>
