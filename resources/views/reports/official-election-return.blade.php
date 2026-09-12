<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Resmi - {{ $election->name }}</title>

    <!-- Tailwind CSS v4 Browser CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
</head>
<body class="text-slate-900 bg-slate-100 font-sans antialiased selection:bg-blue-600 selection:text-white py-6 sm:py-10">

    <!-- Floating Print Control Bar (Hidden on Print) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 backdrop-blur-md text-white p-2 rounded-2xl shadow-xl border border-slate-700">
        <a href="{{ url()->previous() ?: route('admin.elections.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
        <button type="button" onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Dokumen / Simpan PDF</span>
        </button>
    </div>

    <!-- Main Printable A4 Paper Document -->
    <div class="max-w-4xl mx-auto bg-white border border-slate-200 shadow-md sm:rounded-2xl p-8 sm:p-14 print-card space-y-6">
        
        <!-- Letterhead / Kop Surat Resmi -->
        <div class="avoid-break">
            <div class="flex items-center justify-between gap-6 pb-2">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-2xl shrink-0">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold uppercase tracking-wider text-slate-900">
                            {{ $organization?->name ?? 'Komisi Pemilihan Umum Mahasiswa' }}
                        </h2>
                        <p class="text-xs text-slate-600 font-medium">Panitia Pemilihan Mandiri &amp; Terpadu Tahun {{ date('Y') }}</p>
                        <p class="text-[11px] text-slate-500">{{ $organization?->address ?? 'Sekretariat Utama Panitia Pemilihan' }}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-mono text-[10px] font-bold border border-slate-300">
                        DOKUMEN RESMI
                    </span>
                    <p class="text-[10px] font-mono text-slate-500 mt-1">{{ $documentNumber }}</p>
                </div>
            </div>
            <!-- Dual Line Formal Kop Divider -->
            <div class="kop-divider"></div>
        </div>

        <!-- Document Title -->
        <div class="text-center space-y-1 py-1 avoid-break">
            <h1 class="text-base sm:text-lg font-black uppercase tracking-tight text-slate-900">
                BERITA ACARA HASIL PENGHITUNGAN SUARA
            </h1>
            <p class="text-xs font-semibold text-slate-700 uppercase tracking-wider">
                Nomor: {{ $documentNumber }}
            </p>
        </div>

        <!-- Opening Statement -->
        <div class="text-xs sm:text-sm text-slate-800 leading-relaxed text-justify space-y-2 avoid-break">
            <p>
                Pada hari ini, <strong>{{ $generated_at }}</strong>, telah dilaksanakan rekapitulasi dan penutupan resmi penghitungan suara secara elektronik (E-Voting) untuk sesi pemilihan:
            </p>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <p class="font-bold text-slate-900 text-sm">{{ $election->name }}</p>
                <p class="text-xs text-slate-600 mt-0.5">Periode Pelaksanaan: {{ $election->start_at->format('d M Y H:i') }} s/d {{ $election->end_at->format('d M Y H:i') }} {{ \App\Enums\IndonesianTimezone::currentAbbr() }}</p>
            </div>
        </div>

        <!-- Official Statistics Section -->
        <div class="space-y-2.5 avoid-break">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-300 pb-1">
                I. Ringkasan Partisipasi Pemilih
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs stat-grid">
                <div class="p-2.5 rounded-lg border border-slate-300 bg-slate-50">
                    <span class="text-slate-500 block text-[11px]">Daftar Pemilih Tetap (DPT):</span>
                    <strong class="text-slate-900 text-base font-bold">{{ number_format($statistics['total_voters']) }}</strong> Pemilih
                </div>
                <div class="p-2.5 rounded-lg border border-slate-300 bg-slate-50">
                    <span class="text-slate-500 block text-[11px]">Total Suara Masuk:</span>
                    <strong class="text-blue-700 text-base font-bold">{{ number_format($statistics['total_ballots']) }}</strong> Surat Suara
                </div>
                <div class="p-2.5 rounded-lg border border-slate-300 bg-slate-50">
                    <span class="text-slate-500 block text-[11px]">Tingkat Partisipasi:</span>
                    <strong class="text-emerald-700 text-base font-bold">{{ $statistics['turnout_percentage'] }}%</strong>
                </div>
                <div class="p-2.5 rounded-lg border border-slate-300 bg-slate-50">
                    <span class="text-slate-500 block text-[11px]">Status Pemilihan:</span>
                    <strong class="text-slate-800 text-xs font-bold uppercase">{{ $statistics['status_label'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Official Results Table -->
        <div class="space-y-2.5 avoid-break">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 border-b border-slate-300 pb-1">
                II. Rekapitulasi Perolehan Suara Sah
            </h3>

            @if ($results['has_groups'])
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="w-16 text-center">No.</th>
                            <th>Nama Pasangan Calon</th>
                            <th class="text-right w-36">Perolehan Suara</th>
                            <th class="text-right w-28">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results['group_results'] as $idx => $g)
                            <tr class="{{ $idx === 0 && $g['votes'] > 0 ? 'bg-amber-50/50 font-semibold' : '' }}">
                                <td class="text-center font-bold">{{ $g['number'] ?? ($idx + 1) }}</td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $g['name'] }}</div>
                                    @if (! empty($g['slogan']))
                                        <div class="text-[10px] text-slate-500 italic mt-0.5">"{{ $g['slogan'] }}"</div>
                                    @endif
                                </td>
                                <td class="text-right font-bold">{{ number_format($g['votes']) }} Suara</td>
                                <td class="text-right font-bold text-blue-700">{{ $g['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @foreach ($results['position_results'] as $posRes)
                    <div class="space-y-1.5 mt-3">
                        <h4 class="font-bold text-slate-800 text-xs uppercase">{{ $posRes['position_name'] }}</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th class="w-16 text-center">No.</th>
                                    <th>Nama Kandidat</th>
                                    <th class="text-right w-36">Perolehan Suara</th>
                                    <th class="text-right w-28">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posRes['candidates'] as $idx => $c)
                                    <tr>
                                        <td class="text-center font-bold">{{ $c['number'] ?? ($idx + 1) }}</td>
                                        <td class="font-bold text-slate-900">{{ $c['name'] }}</td>
                                        <td class="text-right font-bold">{{ number_format($c['votes']) }} Suara</td>
                                        <td class="text-right font-bold text-blue-700">{{ $c['percentage'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Cryptographic Tamper-Proof Seal -->
        <div class="p-3 rounded-xl border border-slate-300 bg-slate-50 text-xs space-y-1.5 seal-box avoid-break">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-900 uppercase text-[10px] tracking-wider">
                    <i class="fa-solid fa-lock text-emerald-600 mr-1"></i> Segel Kriptografis Digital (SHA-256 Checksum)
                </span>
                <span class="text-[10px] text-slate-500 font-mono">INTEGRITY_VERIFIED</span>
            </div>
            <p class="font-mono text-[10px] text-slate-800 bg-white p-2 rounded border border-slate-300 break-all select-all font-semibold">
                {{ $integrity_checksum }}
            </p>
            <p class="text-[10px] text-slate-500 leading-tight">
                *Dokumen ini sah dan terikat secara digital. Perubahan data surat suara akan menggugurkan validitas checksum di atas.
            </p>
        </div>

        <!-- Signatures Grid -->
        <div class="pt-4 space-y-3 signatures-grid avoid-break">
            <p class="text-xs text-slate-800 text-center font-medium">
                Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandatangani oleh Panitia Pemilihan serta Saksi-Saksi yang hadir.
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 pt-3 text-center text-xs">
                <!-- Ketua Panitia -->
                <div class="space-y-12">
                    <p class="font-semibold text-slate-700">Ketua Panitia Pemilihan,</p>
                    <div>
                        <p class="font-bold text-slate-900 underline">( .................................................. )</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">NIM / NIP: ..............................</p>
                    </div>
                </div>

                <!-- Sekretaris Panitia -->
                <div class="space-y-12">
                    <p class="font-semibold text-slate-700">Sekretaris Panitia,</p>
                    <div>
                        <p class="font-bold text-slate-900 underline">( .................................................. )</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">NIM / NIP: ..............................</p>
                    </div>
                </div>

                <!-- Saksi Paslon -->
                <div class="space-y-12">
                    <p class="font-semibold text-slate-700">Saksi Resmi Paslon / Calon,</p>
                    <div>
                        <p class="font-bold text-slate-900 underline">( .................................................. )</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Saksi Terverifikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
