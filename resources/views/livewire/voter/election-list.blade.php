<div class="space-y-8">
    @if ($user->isUsingDefaultPassword())
        <!-- Security Hero Warning: Default Password Detected -->
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white shadow-xl shadow-red-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden border border-red-400/30">
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-2xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-black/20 text-white text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-red-300 animate-pulse"></span>
                            Tindakan Keamanan Diperlukan
                        </span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight text-white">Segera Ganti Kata Sandi Bawaan Anda!</h2>
                    <p class="text-white/90 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Akun Anda masih menggunakan kata sandi standar sistem (<code class="bg-black/25 px-1.5 py-0.5 rounded font-mono text-white text-xs">password</code>). Harap segera ubah kata sandi baru demi menjaga kerahasiaan hak suara dan keamanan akun Anda.
                    </p>
                </div>
            </div>

            <div class="relative z-10 shrink-0 w-full md:w-auto">
                <a href="{{ route('voter.profile') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-slate-900 hover:bg-slate-50 font-black text-xs shadow-lg transition active:scale-[0.98]">
                    <i class="fa-solid fa-key text-red-600 text-sm"></i>
                    <span>Ganti Password Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                </a>
            </div>
        </div>
    @endif

    @php
        $totalRegistered = $voterStatuses->count();
        $totalVoted = $voterStatuses->where('has_voted', true)->count();
        $pendingVote = $voterStatuses->where('has_voted', false)->where('is_eligible', true)->count();
    @endphp

    <!-- Welcome Voter Card with Dynamic Metrics -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white shadow-xl shadow-blue-500/15 relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold border border-white/20 shadow-xs">
                        <i class="fa-solid fa-circle-check text-emerald-300 text-[10px]"></i>
                        Pemilih Terdaftar (DPT)
                    </span>
                    @if ($user->identifier)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/20 backdrop-blur-md text-blue-100 text-xs font-mono border border-white/10">
                            <i class="fa-solid fa-id-card text-[10px] text-blue-300"></i>
                            {{ $user->identifier }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm leading-relaxed">
                    Gunakan hak suara Anda secara rahasia, bebas, dan terpercaya. Seluruh pilihan dienkripsi dengan standar kriptografi zero-knowledge token.
                </p>
            </div>

            <!-- Quick Voter Stats Chips -->
            <div class="grid grid-cols-3 gap-3 shrink-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3 sm:p-4 text-center">
                    <div class="text-[10px] sm:text-xs text-blue-200 font-semibold uppercase tracking-wider">Sesi Terdaftar</div>
                    <div class="text-xl sm:text-2xl font-black text-white mt-0.5">{{ $totalRegistered }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3 sm:p-4 text-center">
                    <div class="text-[10px] sm:text-xs text-emerald-200 font-semibold uppercase tracking-wider">Sudah Memilih</div>
                    <div class="text-xl sm:text-2xl font-black text-emerald-300 mt-0.5">{{ $totalVoted }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3 sm:p-4 text-center">
                    <div class="text-[10px] sm:text-xs text-amber-200 font-semibold uppercase tracking-wider">Belum Memilih</div>
                    <div class="text-xl sm:text-2xl font-black text-amber-300 mt-0.5">{{ $pendingVote }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Elections Grid Section -->
    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-blue-600"></i>
                    <span>Daftar Sesi Pemilihan</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih sesi pemilihan aktif untuk memberikan hak suara atau memantau hasil realtime.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($activeElections as $election)
                @php
                    $voterStatus = $voterStatuses[$election->id] ?? null;
                    $hasVoted = $voterStatus?->has_voted ?? false;
                    $isEligible = $voterStatus?->is_eligible ?? false;
                    $isRegistered = $voterStatus !== null;
                    $candidateCount = $election->candidate_groups_count > 0 
                        ? $election->candidate_groups_count . ' Paslon' 
                        : ($election->positions_count > 0 ? $election->positions_count . ' Posisi' : 'Kandidat');
                @endphp

                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-lg transition-all duration-200 p-6 flex flex-col justify-between space-y-5 relative">
                    <div class="space-y-4">
                        <!-- Top Badges: Session Status & Voter Participation -->
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shadow-2xs {{ $election->status->badgeClass() }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $election->status->dotClass() }}"></span>
                                {{ $election->status->label() }}
                            </span>

                            @if ($hasVoted)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Sudah Memilih
                                </span>
                            @elseif ($isRegistered && $isEligible)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200/80 text-[11px] font-bold shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    Belum Memilih
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200/80 text-[11px] font-medium shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Bukan DPT
                                </span>
                            @endif
                        </div>

                        <!-- Election Title & Info -->
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base leading-snug">
                                {{ $election->name }}
                            </h3>

                            @if ($election->description)
                                <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $election->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Schedule & Candidates Info Pill -->
                        <div class="space-y-2 pt-1">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 truncate">
                                    <i class="fa-regular fa-clock text-slate-400"></i>
                                    <span class="truncate">Selesai: {{ $election->end_at->format('d M Y, H:i') }} {{ \App\Enums\IndonesianTimezone::currentAbbr() }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200/80 text-[10px] font-bold text-slate-700 shrink-0">
                                    {{ $candidateCount }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center gap-2">
                        @if ($hasVoted)
                            <a href="{{ route('ballot.verify') }}" class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-semibold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5" title="Lihat Bukti Verifikasi Surat Suara">
                                <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
                                <span>Bukti Suara</span>
                            </a>
                            <a href="{{ route('voter.elections.results', $election) }}" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs text-center shadow-xs transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-chart-column text-xs"></i>
                                <span>Hasil</span>
                            </a>
                        @elseif ($isRegistered && $isEligible && $election->isActive())
                            <a href="{{ route('voter.elections.vote', $election) }}" class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center shadow-md shadow-blue-500/25 transition active:scale-[0.98] flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-check-to-slot text-xs"></i>
                                <span>Masuk Bilik Suara</span>
                            </a>
                        @else
                            <button disabled class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs text-center cursor-not-allowed">
                                {{ ! $isRegistered ? 'Bukan DPT' : (! $isEligible ? 'Hak Dicabut' : 'Belum Dibuka') }}
                            </button>
                        @endif

                        <a href="{{ route('screen.live', ['election' => $election->slug ?: $election->id]) }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Buka Layar Monitor (Tanpa Menu)">
                            <i class="fa-solid fa-desktop text-xs"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Belum Ada Sesi Pemilihan Aktif</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                        Saat ini belum ada sesi pemilihan yang sedang dibuka untuk pemilih. Silakan cek kembali saat sesi pemilihan dimulai.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $activeElections->links() }}
        </div>
    </div>
</div>
