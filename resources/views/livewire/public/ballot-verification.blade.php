<div class="min-h-screen bg-slate-50 flex flex-col justify-between">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/report.css') }}">
    @endpush

    <!-- Clean Light Top Bar (Hidden on Print) -->
    <header class="bg-white border-b border-slate-200 px-6 sm:px-10 py-4 shadow-xs no-print">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('login') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    <i class="fa-solid fa-check-to-slot"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 tracking-tight leading-tight">E-Voting System</h1>
                    <p class="text-[11px] text-slate-500 font-medium">Pusat Verifikasi Surat Suara</p>
                </div>
            </a>

            <div class="flex items-center gap-2.5 text-xs">
                <a href="{{ route('screen.live') }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                    <i class="fa-solid fa-desktop mr-1 text-slate-500"></i> Layar Monitor
                </a>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('voter.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold transition">
                        Dashboard Saya <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Verification Center -->
    <main class="flex-1 max-w-3xl w-full mx-auto px-6 py-8 sm:py-10 space-y-6">
        <!-- Title Header (Hidden on Print) -->
        <div class="text-center space-y-1.5 no-print">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mx-auto mb-2 shadow-xs border border-blue-100">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Verifikasi Keabsahan Surat Suara</h2>
            <p class="text-xs text-slate-500 max-w-lg mx-auto">
                Status keikutsertaan dan keabsahan surat suara Anda dijamin dan dicatat secara terenkripsi di kotak suara digital.
            </p>
        </div>

        <!-- Logged-in Voter Quick Auto-Verification Bar (Hidden on Print) -->
        @auth
            @if ($voterElections->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3 no-print">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-check text-blue-600"></i>
                            <span>Hak Pilih Akun Anda ({{ auth()->user()->name }})</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Verifikasi Otomatis Aktif</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-{{ min(2, $voterElections->count()) }} gap-2.5">
                        @foreach ($voterElections as $ev)
                            <button 
                                type="button" 
                                wire:click="verifyVoterElection('{{ $ev->id }}')"
                                class="p-3.5 rounded-xl border text-left transition cursor-pointer flex items-center justify-between gap-3 {{ ($selectedElectionId === $ev->election_id) ? 'bg-blue-50/70 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 border-slate-200' }}"
                            >
                                <div class="overflow-hidden">
                                    <p class="font-bold text-xs text-slate-900 truncate">{{ $ev->election?->name }}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">
                                        @if ($ev->has_voted)
                                            <span class="text-emerald-700 font-bold"><i class="fa-solid fa-check mr-1"></i>Sudah Memilih</span> &bull; {{ $ev->voted_at?->format('d M Y, H:i') }}
                                        @else
                                            <span class="text-amber-700 font-semibold"><i class="fa-solid fa-clock mr-1"></i>Belum Digunakan</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 shrink-0">
                                    {{ ($selectedElectionId === $ev->election_id) ? 'Terpilih' : 'Cek Status' }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth

        <!-- Verified Result Certificate Card -->
        @if ($verifiedBallot)
            <div class="bg-white rounded-2xl border-2 border-emerald-400 p-6 sm:p-8 shadow-md space-y-5 print-card avoid-break">
                
                <!-- Print Letterhead (Formal Indonesian Header - Only displayed during Print) -->
                <div class="hidden only-print avoid-break">
                    <div class="flex items-center justify-between gap-4 pb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xl shrink-0">
                                <i class="fa-solid fa-check-to-slot"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">KOMISI PEMILIHAN UMUM (E-VOTING)</h2>
                                <p class="text-[11px] font-semibold text-slate-700">SERTIFIKAT BUKTI VERIFIKASI KEIKUTSERTAAN SUARA SAH</p>
                                <p class="text-[10px] text-slate-500">Sistem E-Voting Terpadu &bull; Integritas Data Anti-Manipulasi</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block px-2.5 py-0.5 rounded bg-slate-100 font-mono text-[9px] font-bold border border-slate-300">BUKTI RESMI</span>
                            <p class="text-[9px] font-mono text-slate-500 mt-0.5">{{ date('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="kop-divider"></div>
                </div>

                <!-- Status Badge Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-200 shrink-0">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                <i class="fa-solid fa-circle-check mr-1"></i> Terverifikasi &amp; Sah
                            </span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mt-0.5">Surat Suara Resmi Tercatat di Kotak Suara</h3>
                        </div>
                    </div>
                    <span class="text-xs font-mono text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded border border-emerald-200 shrink-0">
                        SEAL_VALID
                    </span>
                </div>

                <!-- Verified Details Grid / Formal Table -->
                <table class="report-table">
                    <tbody>
                        @if (! empty($verifiedBallot['voter_name']))
                            <tr>
                                <th class="w-44 text-left">Nama Pemilih</th>
                                <td class="font-bold text-slate-900">{{ $verifiedBallot['voter_name'] }}</td>
                            </tr>
                        @endif
                        @if (! empty($verifiedBallot['voter_identifier']))
                            <tr>
                                <th class="text-left">Identitas Pemilih (NIM / NIK)</th>
                                <td class="font-mono text-slate-800 font-semibold">{{ $verifiedBallot['voter_identifier'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="w-44 text-left">Sesi Pemilihan</th>
                            <td class="font-bold text-slate-900">{{ $verifiedBallot['election_name'] }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Waktu Pencatatan Suara</th>
                            <td class="font-semibold text-slate-800">{{ $verifiedBallot['submitted_at'] }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Status Keabsahan</th>
                            <td>
                                <span class="font-bold text-emerald-700 uppercase"><i class="fa-solid fa-circle-check mr-1"></i>SAH (Tercatat di Database)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Cryptographic Token Proof -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-300 text-xs space-y-1.5 seal-box avoid-break">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 uppercase tracking-wider text-[10px]">
                            <i class="fa-solid fa-lock text-emerald-600 mr-1"></i> Segel Kriptografis Digital (SHA-256 Hash Token)
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono">SEAL_AUTHENTIC</span>
                    </div>
                    <div class="font-mono text-[10px] text-slate-800 bg-white p-2 rounded-lg border border-slate-200 break-all select-all font-semibold">
                        {{ $verifiedBallot['token_hash'] }}
                    </div>
                </div>

                <!-- Zero-Knowledge Privacy Guarantee -->
                <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-900 text-xs flex items-start gap-2.5 avoid-break">
                    <i class="fa-solid fa-shield-halved text-blue-600 text-sm mt-0.5 shrink-0"></i>
                    <div class="space-y-0.5">
                        <strong class="font-bold block text-xs">Jaminan Kerahasiaan Pilihan (Zero-Knowledge Vote Privacy):</strong>
                        <p class="text-[11px] text-blue-800 leading-relaxed">
                            Sesuai standar pemungutan suara demokratis &amp; aman, surat suara Anda telah dihitung secara sah di dalam kotak suara tanpa menghubungkan nama pemilih dengan kandidat pilihan Anda.
                        </p>
                    </div>
                </div>

                <!-- Official Verification Stamp Block (Print Mode) -->
                <div class="hidden only-print pt-3 avoid-break">
                    <div class="flex items-center justify-between text-xs text-slate-700 pt-2 border-t border-slate-200">
                        <div>
                            <p class="font-bold text-slate-900">PANITIA PEMILIHAN TERPADU</p>
                            <p class="text-[10px] text-slate-500">Dokumen bukti verifikasi digital yang sah dan tidak dapat dipalsukan.</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-3 py-1 rounded border-2 border-emerald-600 text-emerald-800 font-black text-[11px] uppercase tracking-wider">
                                [ LULUS VERIFIKASI RESMI ]
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Hidden on Print) -->
                <div class="flex items-center justify-end gap-2 pt-1 no-print">
                    <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition cursor-pointer flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Bukti Verifikasi / Simpan PDF</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Manual Token Check Option (Hidden on Print) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4 no-print">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <span>Cek Menggunakan Kode Token / UUID Manual</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Opsi ini berguna jika Anda memverifikasi tanda terima fisik atau token dari perangkat lain.
                </p>
            </div>

            <form wire:submit="verify" class="space-y-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-barcode text-xs"></i>
                    </span>
                    <input 
                        wire:model="searchToken" 
                        type="text" 
                        placeholder="Ketik atau tempel kode token / UUID tanda terima di sini..."
                        class="w-full pl-9 pr-24 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs font-mono outline-none transition bg-white"
                    />
                    <button 
                        type="submit" 
                        class="absolute right-1 top-1 bottom-1 px-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer flex items-center gap-1 shadow-xs"
                    >
                        <span>Cari</span>
                    </button>
                </div>
            </form>

            @if ($hasSearched && ! $verifiedBallot)
                <!-- Not Found Alert -->
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <div class="flex items-center gap-2 font-bold">
                        <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                        <span>Kode Token / Surat Suara Tidak Ditemukan</span>
                    </div>
                    <p class="text-rose-700 text-[11px] leading-relaxed">
                        Kode token / UUID surat suara yang Anda masukkan tidak cocok dengan data terdaftar. Pastikan seluruh karakter kode disalin dengan lengkap.
                    </p>
                </div>
            @endif
        </div>
    </main>

    <!-- Clean Footer (Hidden on Print) -->
    <footer class="bg-white border-t border-slate-200 px-6 sm:px-10 py-4 text-xs text-slate-500 text-center no-print">
        <p>&copy; {{ date('Y') }} Sistem E-Voting Terpadu &bull; Dilindungi Row-Level Locking &amp; Zero-Knowledge Verification.</p>
    </footer>
</div>
