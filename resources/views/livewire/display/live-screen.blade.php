<div wire:poll.2s class="min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-blue-600 selection:text-white">
    @php
        $siteLogo = \App\Models\Setting::get('app_logo');
        $siteAppName = \App\Models\Setting::get('app_name', 'E-Voting');
    @endphp
    <!-- Top Broadcast Header -->
    <header class="bg-white border-b border-slate-200/90 px-6 lg:px-12 py-3.5 shrink-0 shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Left: Title & Live Badge -->
            <div class="flex items-center gap-3.5">
                @if ($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteAppName }}" class="w-10 h-10 rounded-xl object-contain shadow-xs shrink-0 bg-white border border-slate-200/80 p-1">
                @else
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                        <i class="fa-solid fa-check-to-slot"></i>
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse mr-1.5"></span>
                            LIVE QUICK COUNT
                        </span>
                        <span class="text-xs text-slate-400 font-medium hidden sm:inline">&bull; Data Resmi Terverifikasi</span>
                    </div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-tight mt-0.5">
                        {{ $election?->name ?? 'Penghitungan Suara Realtime' }}
                    </h1>
                </div>
            </div>

            <!-- Right: Live Turnout Pill, Clock, Election Selector & Fullscreen -->
            <div class="flex items-center gap-3">
                @if ($stats)
                    <div class="hidden lg:flex items-center gap-3 px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">Partisipasi:</span>
                            <span class="font-bold text-blue-600">{{ $stats['turnout_percentage'] }}%</span>
                        </div>
                        <span class="text-slate-300">|</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-500 font-medium">Suara Masuk:</span>
                            <span class="font-bold text-slate-800">{{ number_format($stats['total_ballots']) }} / {{ number_format($stats['total_voters']) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Digital Clock (Isolated from wire:poll with wire:ignore) -->
                <div wire:ignore class="text-right hidden sm:block border-l border-slate-200 pl-3">
                    <div id="live-digital-clock" class="text-sm font-bold text-slate-900 tracking-wider font-mono">--:--:--</div>
                    <div id="live-digital-date" class="text-[10px] text-slate-500 font-medium">Hari ini</div>
                </div>


                <!-- Fullscreen Button -->
                <button 
                    type="button" 
                    data-toggle-fullscreen 
                    class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-semibold text-xs flex items-center gap-1.5 transition cursor-pointer shadow-xs"
                    title="Layar Penuh (F11)"
                >
                    <i class="fa-solid fa-expand text-slate-600"></i>
                    <span class="hidden md:inline">Layar Penuh</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Presentation Stage -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-6 lg:py-8 flex flex-col justify-center space-y-6">
        @if ($election && $stats)
            <!-- Top Quick Metrics Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3 shadow-xs">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Suara Masuk</p>
                        <p class="text-xl font-bold text-slate-900 leading-tight">{{ number_format($stats['total_ballots']) }} <span class="text-xs font-normal text-slate-500">suara</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3 shadow-xs">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Partisipasi (Turnout)</p>
                        <p class="text-xl font-bold text-emerald-600 leading-tight">{{ $stats['turnout_percentage'] }}%</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3 shadow-xs">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">DPT Terdaftar</p>
                        <p class="text-xl font-bold text-slate-900 leading-tight">{{ number_format($stats['total_voters']) }} <span class="text-xs font-normal text-slate-500">pemilih</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center gap-3 shadow-xs">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-base shrink-0">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Status Sesi</p>
                        <p class="text-sm font-bold text-slate-800 leading-tight flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full {{ $election->isActive() ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $stats['status_label'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Candidate Podium Cards Grid -->
            @if ($stats['results']['has_groups'])
                @php
                    $groupResults = $stats['results']['group_results'];
                    $isTieLeader = count($groupResults) > 1 && $groupResults[0]['votes'] === $groupResults[1]['votes'];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-{{ min(3, max(1, count($groupResults))) }} gap-5 items-stretch">
                    @foreach ($groupResults as $idx => $g)
                        @php
                            $isStrictLeader = ! $isTieLeader && $idx === 0 && $g['votes'] > 0;
                        @endphp
                        <div class="bg-white rounded-2xl border-2 {{ $isStrictLeader ? 'border-amber-400 shadow-md ring-2 ring-amber-400/20' : 'border-slate-200 shadow-xs' }} p-6 flex flex-col justify-between space-y-5 relative transition-all">
                            
                            <!-- Top Status Badge -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white font-black text-base flex items-center justify-center shadow-xs">
                                        {{ $g['number'] ?? ($idx + 1) }}
                                    </span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Paslon {{ $g['number'] }}</span>
                                </div>

                                @if ($isStrictLeader)
                                    <span class="px-2.5 py-1 rounded-full bg-amber-400 text-slate-900 font-bold text-[10px] shadow-xs flex items-center gap-1 uppercase tracking-wider">
                                        <i class="fa-solid fa-star text-[9px]"></i> Memimpin
                                    </span>
                                @endif
                            </div>

                            <!-- Presidential Duo Portraits Area -->
                            @if (! empty($g['members']))
                                <div class="grid grid-cols-2 gap-3 py-1">
                                    @foreach ($g['members'] as $m)
                                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col items-center text-center space-y-2">
                                            @if ($m['photo'])
                                                <img src="{{ \Illuminate\Support\Str::startsWith($m['photo'], 'http') ? $m['photo'] : asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border border-slate-200 shadow-xs" />
                                            @else
                                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-slate-200 text-slate-700 font-bold text-xl flex items-center justify-center border border-slate-200">
                                                    {{ strtoupper(substr($m['name'], 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="w-full">
                                                <p class="text-xs font-bold text-slate-900 truncate">{{ $m['name'] }}</p>
                                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[9px] font-semibold uppercase {{ $m['sort_order'] === 1 ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700' }}">
                                                    {{ $m['sort_order'] === 1 ? 'Calon Ketua' : 'Calon Wakil' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Names & Slogan -->
                            <div class="text-center space-y-1">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                                    {{ $g['name'] }}
                                </h3>
                                @if (! empty($g['slogan']))
                                    <p class="text-xs text-slate-500 italic line-clamp-1">"{{ $g['slogan'] }}"</p>
                                @endif
                            </div>

                            <!-- Visi & Misi Box -->
                            @if (! empty($g['vision']) || ! empty($g['mission']))
                                <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 text-left space-y-2 text-xs">
                                    @if (! empty($g['vision']))
                                        <div>
                                            <span class="font-bold text-[10px] uppercase tracking-wider text-blue-600 block">Visi:</span>
                                            <p class="text-slate-700 text-[11px] leading-relaxed line-clamp-2">{{ $g['vision'] }}</p>
                                        </div>
                                    @endif
                                    @if (! empty($g['mission']))
                                        <div>
                                            <span class="font-bold text-[10px] uppercase tracking-wider text-emerald-600 block">Misi:</span>
                                            <p class="text-slate-600 text-[11px] leading-relaxed line-clamp-2 whitespace-pre-line">{{ $g['mission'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Percentage & Progress Bar Box -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs font-semibold text-slate-500">Perolehan Suara:</span>
                                    <div class="text-right">
                                        <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $g['percentage'] }}%</span>
                                        <span class="text-xs font-semibold text-slate-500 ml-1">({{ number_format($g['votes']) }} Suara)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                    <div class="h-full rounded-full {{ $isStrictLeader ? 'bg-amber-500' : 'bg-blue-600' }} transition-all duration-500" data-progress-width="{{ $g['percentage'] }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Multi Position Screen Display -->
                <div class="space-y-5">
                    @foreach ($stats['results']['position_results'] as $pos)
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-xs">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $pos['position_name'] }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $pos['position_description'] ?? 'Posisi Pemilihan Mahasiswa' }}</p>
                                </div>
                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">Total: {{ number_format($pos['total_votes']) }} Suara</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($pos['candidates'] as $c)
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 overflow-hidden">
                                                @if ($c['photo'])
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($c['photo'], 'http') ? $c['photo'] : asset('storage/' . $c['photo']) }}" alt="{{ $c['name'] }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-xs shrink-0" />
                                                @else
                                                    <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center shrink-0">
                                                        {{ strtoupper(substr($c['name'], 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="overflow-hidden">
                                                    <span class="font-bold text-slate-900 text-sm block truncate">
                                                        {{ $c['number'] ?? '#' }}. {{ $c['name'] }}
                                                    </span>
                                                    @if (! empty($c['slogan']))
                                                        <span class="text-[11px] text-slate-500 italic block truncate">"{{ $c['slogan'] }}"</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <span class="font-extrabold text-slate-900 text-lg">{{ $c['percentage'] }}%</span>
                                                <span class="text-xs text-slate-500 block">({{ number_format($c['votes']) }} Suara)</span>
                                            </div>
                                        </div>

                                        @if (! empty($c['vision']) || ! empty($c['mission']))
                                            <div class="bg-white/90 rounded-lg p-2.5 border border-slate-100 text-left space-y-1 text-xs">
                                                @if (! empty($c['vision']))
                                                    <p class="text-[11px] text-slate-700 leading-snug line-clamp-2"><span class="font-bold text-blue-600">Visi:</span> {{ $c['vision'] }}</p>
                                                @endif
                                                @if (! empty($c['mission']))
                                                    <p class="text-[11px] text-slate-600 leading-snug line-clamp-2"><span class="font-bold text-emerald-600">Misi:</span> {{ $c['mission'] }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-full bg-blue-600 rounded-full transition-all duration-500" data-progress-width="{{ $c['percentage'] }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <!-- Selection Stage: No Election Selected -->
            <div class="max-w-5xl mx-auto w-full space-y-6 my-auto py-8">
                <div class="text-center space-y-2 mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 text-2xl shadow-xs mb-2">
                        <i class="fa-solid fa-desktop"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pilih Sesi Pemilihan</h2>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto leading-relaxed">
                        Silakan pilih salah satu sesi pemilihan di bawah ini untuk menampilkan data perolehan suara (quick count), grafik persentase, dan statistik partisipasi pada layar monitor.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse ($allElections as $el)
                        <a 
                            href="{{ route('screen.live', ['election' => $el->slug ?: $el->id]) }}" 
                            class="bg-white rounded-2xl border-2 border-slate-200 hover:border-blue-500 p-6 flex flex-col justify-between space-y-5 shadow-xs hover:shadow-md transition cursor-pointer group block"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shadow-2xs {{ $el->status->badgeClass() }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $el->status->dotClass() }}"></span>
                                        {{ $el->status->label() }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        @if ($el->candidate_groups_count > 0)
                                            {{ $el->candidate_groups_count }} Paslon
                                        @else
                                            {{ $el->positions_count }} Posisi
                                        @endif
                                    </span>
                                </div>

                                <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-blue-600 transition">
                                    {{ $el->name }}
                                </h3>

                                @if ($el->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $el->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <div class="text-[11px] text-slate-400">
                                    <span>{{ number_format($el->ballots_count) }} suara masuk</span>
                                </div>
                                <span class="inline-flex items-center gap-1 text-blue-600 font-bold group-hover:translate-x-0.5 transition">
                                    Tampilkan Monitor <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-md mx-auto space-y-3 shadow-xs">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                                <i class="fa-solid fa-calendar-xmark"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Belum Ada Sesi Pemilihan</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Belum ada sesi pemilihan yang dipublikasikan atau aktif untuk ditampilkan di layar monitor.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </main>

    <!-- Bottom Broadcast Footer -->
    <footer class="bg-white border-t border-slate-200 px-6 lg:px-12 py-3 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Sistem E-Voting Terpadu &bull; Enkripsi Anonim &amp; Terverifikasi</span>
        </div>
        <div class="flex items-center gap-4 text-slate-400">
            <span>Sinkronisasi Terakhir: <strong class="text-slate-700 font-mono">{{ $stats['updated_at'] ?? now()->format('H:i:s') }}</strong></span>
            <span>Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[10px] border border-slate-300">F11</kbd> untuk Layar Penuh</span>
        </div>
    </footer>

    <!-- Realtime Vote Toast Notification Container -->
    <div wire:ignore id="live-vote-toast-container" class="fixed bottom-14 right-3 left-3 sm:left-auto sm:right-6 sm:max-w-sm z-50 flex flex-col gap-2 pointer-events-none"></div>
</div>

@push('scripts')
<script src="{{ asset('js/live-screen.js') }}"></script>
@endpush
