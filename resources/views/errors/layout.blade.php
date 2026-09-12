<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem E-Voting</title>

    <!-- Tailwind CSS v4 Browser CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <div class="max-w-md w-full text-center">
        <!-- Error Illustration / Icon Badge -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl @yield('icon-bg', 'bg-rose-50 text-rose-600') text-3xl font-bold shadow-lg mb-6 border @yield('icon-border', 'border-rose-100')">
            @yield('icon')
        </div>

        <!-- Error Code Pill -->
        <div class="mb-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold tracking-wider uppercase @yield('badge-class', 'bg-rose-100 text-rose-800')">
                <span class="w-2 h-2 rounded-full @yield('badge-dot', 'bg-rose-500')"></span>
                Error @yield('code')
            </span>
        </div>

        <!-- Title & Description -->
        <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-2">
            @yield('headline')
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-sm mx-auto mb-8">
            @yield('message')
        </p>

        <!-- Security Guarantee Notice (OWASP A05: No Debug/Stacktrace Leakage) -->
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs text-[11px] text-slate-500 mb-6 flex items-center justify-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
            <span>Integritas sistem e-voting tetap aman dan terlindungi.</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('voter.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition cursor-pointer">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition cursor-pointer">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Masuk ke Akun</span>
                </a>
            @endauth

            <button type="button" onclick="window.location.reload();" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-xs transition cursor-pointer">
                <i class="fa-solid fa-rotate-right text-xs"></i>
                <span>Muat Ulang</span>
            </button>
        </div>

        <!-- Footer Note -->
        <p class="mt-8 text-[10px] text-slate-400">
            &copy; {{ date('Y') }} Sistem E-Voting Terpadu &bull; Dilindungi Enkripsi Anonim &amp; Audit Log
        </p>
    </div>

</body>
</html>
