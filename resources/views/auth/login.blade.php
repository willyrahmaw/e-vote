<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteFavicon = \App\Models\Setting::get('app_favicon');
        $siteLogo = \App\Models\Setting::get('app_logo');
        $siteAppName = \App\Models\Setting::get('app_name', 'Sistem E-Voting Terpadu');
        $siteTagline = \App\Models\Setting::get('app_tagline', 'Platform Pemilihan Fleksibel, Aman, dan Terverifikasi');
    @endphp
    <title>Masuk - {{ $siteAppName }}</title>
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
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="h-full flex items-center justify-center p-4 auth-bg">

    <div class="max-w-md w-full">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            @if ($siteLogo)
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white p-2 shadow-xs border border-slate-200/80 mb-3">
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteAppName }}" class="w-full h-full object-contain">
                </div>
            @else
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600 text-white text-xl font-bold shadow-xs mb-3">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
            @endif
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $siteAppName }}</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $siteTagline }}</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl p-8 shadow-xs border border-slate-200">
            @if (session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email atau NIM / ID Pemilih</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com atau NIM/NIK"
                            class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs outline-none transition bg-white" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input id="password" type="password" name="password" required
                            placeholder="••••••••"
                            class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs outline-none transition bg-white" />
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition cursor-pointer">
                    Masuk ke Akun
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Akun Demo</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button"
                        onclick="document.getElementById('email').value='admin@example.com';document.getElementById('password').value='password';"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 text-left transition cursor-pointer">
                        <span class="font-semibold text-slate-800 block text-xs">Administrator</span>
                        <span class="text-[10px] text-slate-400 block">admin@example.com</span>
                    </button>
                    <button type="button"
                        onclick="document.getElementById('email').value='voter@example.com';document.getElementById('password').value='password';"
                        class="p-2.5 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 text-left transition cursor-pointer">
                        <span class="font-semibold text-slate-800 block text-xs">Pemilih (DPT)</span>
                        <span class="text-[10px] text-slate-400 block">voter@example.com</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-5">
            <i class="fa-solid fa-shield-halved mr-1 text-slate-400"></i> Terenkripsi & Dilindungi Row Locking Double-Vote Protection
        </p>
    </div>

    <!-- Core App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
</body>
</html>
