<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteFavicon = \App\Models\Setting::get('app_favicon');
        $siteAppName = \App\Models\Setting::get('app_name', 'E-Voting');
    @endphp
    <title>{{ $title ?? $siteAppName }}</title>
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
    @stack('styles')

    @livewireStyles
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    {{ $slot }}

    <!-- Core Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/voting.js') }}"></script>
    <script src="{{ asset('js/live-results.js') }}"></script>
    @stack('scripts')

    @livewireScripts
</body>
</html>
