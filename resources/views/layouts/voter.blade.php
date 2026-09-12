<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteFavicon = \App\Models\Setting::get('app_favicon');
        $siteLogo = \App\Models\Setting::get('app_logo');
        $siteAppName = \App\Models\Setting::get('app_name', 'E-Voting Terpadu');
        $siteOrgName = \App\Models\Setting::get('institution_name', 'Portal Pemilih');
    @endphp
    <title>{{ $title ?? 'Portal Pemilih' }} - {{ $siteAppName }}</title>
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
    <link rel="stylesheet" href="{{ asset('css/voting.css') }}">
    @stack('styles')

    @livewireStyles
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('voter.dashboard') }}" class="flex items-center gap-2.5">
                        @if ($siteLogo)
                            <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteAppName }}" class="w-9 h-9 rounded-xl object-contain shadow-md shadow-blue-500/10">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/20">
                                <i class="fa-solid fa-check-to-slot"></i>
                            </div>
                        @endif
                        <div>
                            <span class="font-bold text-sm tracking-wide text-slate-900 block leading-tight">{{ $siteAppName }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block truncate max-w-[200px]">{{ $siteOrgName }}</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('voter.dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('voter.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-house mr-1.5 text-xs"></i> Dashboard
                    </a>
                    <a href="{{ route('voter.profile') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('voter.profile') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-user-shield mr-1.5 text-xs"></i> Profil &amp; Password
                    </a>
                    <a href="{{ route('ballot.verify') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('ballot.verify') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-shield-halved mr-1.5 text-xs"></i> Verifikasi Suara
                    </a>
                    <a href="{{ route('screen.live') }}" target="_blank" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-desktop mr-1.5 text-xs"></i> Layar Monitor
                    </a>
                </nav>

                <!-- User Profile & Logout -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('voter.profile') }}" class="flex items-center gap-2.5 pl-3 border-l border-slate-200 group hover:opacity-80 transition" title="Kelola Profil & Ganti Password">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <p class="text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition leading-tight max-w-[120px] sm:max-w-none truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-500 hidden sm:block">{{ auth()->user()->identifier ?? 'Pemilih Sah' }}</p>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                            @csrf
                            <button type="submit" title="Keluar" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition cursor-pointer">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Body Slot (with bottom padding for mobile bar) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 pb-24 md:pb-8">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation Bar (Fixed for Mobile Screens) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 py-2 px-3 shadow-lg no-print">
        <div class="flex items-center justify-around text-center">
            <a href="{{ route('voter.dashboard') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition {{ request()->routeIs('voter.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                <i class="fa-solid fa-house text-base"></i>
                <span class="text-[10px]">Dashboard</span>
            </a>

            <a href="{{ route('voter.profile') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition {{ request()->routeIs('voter.profile') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                <i class="fa-solid fa-user-shield text-base"></i>
                <span class="text-[10px]">Profil</span>
            </a>

            <a href="{{ route('ballot.verify') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl transition {{ request()->routeIs('ballot.verify') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                <i class="fa-solid fa-shield-halved text-base"></i>
                <span class="text-[10px]">Verifikasi</span>
            </a>

            <a href="{{ route('screen.live') }}" target="_blank" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl text-slate-500 hover:text-slate-900 transition">
                <i class="fa-solid fa-desktop text-base"></i>
                <span class="text-[10px]">Monitor</span>
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex flex-col items-center gap-1 py-1 px-3 rounded-xl text-slate-400 hover:text-rose-600 transition cursor-pointer">
                        <i class="fa-solid fa-right-from-bracket text-base"></i>
                        <span class="text-[10px]">Keluar</span>
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Privacy & Security Footer (Hidden on Mobile, Clean on Desktop) -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6 hidden md:block no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
                <span>Enkripsi Surat Suara Anonim &amp; Perlindungan Hak Pilih Dijamin.</span>
            </div>
            <p>&copy; {{ date('Y') }} Sistem E-Voting Terdesentralisasi &amp; Anonim.</p>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/voting.js') }}"></script>
    <script src="{{ asset('js/live-results.js') }}"></script>
    @stack('scripts')

    @livewireScripts
</body>
</html>
