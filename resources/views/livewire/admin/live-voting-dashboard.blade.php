<div wire:poll.3s class="space-y-6">
    <!-- Header with Live Pulse Indicator -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping mr-1.5"></span>
                    LIVE STREAMING
                </span>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Live Voting Realtime Monitor</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pembaruan data otomatis setiap 3 detik. Privasi terjamin: hasil murni agregat kalkulasi suara sah.</p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <select wire:model.live="selectedElectionId" class="flex-1 sm:flex-none px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white outline-none">
                @foreach ($elections as $elec)
                    <option value="{{ $elec->id }}">{{ $elec->name }}</option>
                @endforeach
            </select>

            @if ($selectedElectionId)
                <a href="{{ route('admin.elections.report', ['election' => $selectedElectionId]) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-semibold shadow-xs transition" title="Cetak Berita Acara Resmi">
                    <i class="fa-solid fa-file-lines text-emerald-600"></i>
                    <span class="hidden sm:inline">Berita Acara</span>
                </a>
                <a href="{{ route('screen.live', ['election' => $selectedElectionId]) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition" title="Buka Layar Monitor Standalone">
                    <i class="fa-solid fa-desktop text-amber-400"></i>
                    <span class="hidden sm:inline">Layar Monitor</span>
                </a>
            @endif
        </div>
    </div>

    @if ($stats && $currentElection)
        <!-- Live Countdown & Status Box (Clean Light Surface) -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6"
            data-countdown
            data-start="{{ $currentElection->start_at->toIso8601String() }}"
            data-end="{{ $currentElection->end_at->toIso8601String() }}">
            <div>
                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider block" data-countdown-label>
                    Status Sesi Pemilihan:
                </span>
                <h2 class="text-lg font-bold text-slate-900 mt-0.5">{{ $currentElection->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $currentElection->start_at->format('d M Y H:i') }} - {{ $currentElection->end_at->format('d M Y H:i') }}
                </p>
            </div>

            <!-- Countdown Display (Isolated from wire:poll with wire:ignore) -->
            <div wire:ignore class="flex items-center gap-2.5">
                <div class="countdown-box">
                    <span class="text-xl font-extrabold text-slate-900" data-days>00</span>
                    <span class="text-[9px] uppercase font-semibold text-slate-400">Hari</span>
                </div>
                <div class="countdown-box">
                    <span class="text-xl font-extrabold text-slate-900" data-hours>00</span>
                    <span class="text-[9px] uppercase font-semibold text-slate-400">Jam</span>
                </div>
                <div class="countdown-box">
                    <span class="text-xl font-extrabold text-slate-900" data-minutes>00</span>
                    <span class="text-[9px] uppercase font-semibold text-slate-400">Menit</span>
                </div>
                <div class="countdown-box">
                    <span class="text-xl font-extrabold text-slate-900" data-seconds>00</span>
                    <span class="text-[9px] uppercase font-semibold text-slate-400">Detik</span>
                </div>
            </div>
        </div>

        <!-- 4 Stat Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Hak Suara (DPT)</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_voters']) }}</p>
                <div class="mt-2 text-xs text-slate-500 font-medium">Pemilih terdaftar sah</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sudah Menggunakan Hak</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['voted_count']) }}</p>
                <div class="mt-2 text-xs text-emerald-600 font-medium">Telah mencoblos surat suara</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Belum Menggunakan Hak</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['not_voted_count']) }}</p>
                <div class="mt-2 text-xs text-amber-600 font-medium">Menunggu partisipasi</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tingkat Partisipasi (Turnout)</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $stats['turnout_percentage'] }}%</p>
                <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" data-progress-width="{{ $stats['turnout_percentage'] }}"></div>
                </div>
            </div>
        </div>

        <!-- Live Vote Distribution -->
        @if ($stats['results']['has_groups'])
            <!-- Paslon Group Results -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Perolehan Suara Pasangan Calon (Paslon)</h3>
                    <span class="text-xs text-slate-400 font-medium">Total Surat Suara: {{ number_format($stats['total_ballots']) }}</span>
                </div>

                @php
                    $isTieLeader = count($stats['results']['group_results']) > 1 
                        && $stats['results']['group_results'][0]['votes'] === $stats['results']['group_results'][1]['votes'];
                    $topVotes = $stats['results']['group_results'][0]['votes'] ?? 0;
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($stats['results']['group_results'] as $index => $g)
                        @php
                            $isStrictLeader = ! $isTieLeader && $index === 0 && $g['votes'] > 0;
                        @endphp
                        <div class="p-5 rounded-2xl border-2 {{ $isStrictLeader ? 'border-amber-400 bg-amber-50/20' : 'border-slate-200 bg-white' }} space-y-4 relative">
                            @if ($isStrictLeader)
                                <span class="absolute -top-3 right-4 px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-900 font-bold text-[10px] shadow-xs flex items-center gap-1">
                                    <i class="fa-solid fa-star text-[9px]"></i> PEROLEHAN TERTINGGI
                                </span>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-sm flex items-center justify-center">
                                    {{ $g['number'] ?? '#' }}
                                </span>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-slate-900">{{ $g['percentage'] }}%</span>
                                    <span class="text-xs text-slate-500 block">({{ number_format($g['votes']) }} Suara)</span>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-extrabold text-slate-900 text-base">{{ $g['name'] }}</h4>
                                @if (! empty($g['slogan']))
                                    <p class="text-xs text-blue-600 italic mt-0.5">"{{ $g['slogan'] }}"</p>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full bg-blue-600 transition-all duration-500" data-progress-width="{{ $g['percentage'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Multi Position Results -->
            @foreach ($stats['results']['position_results'] as $posResult)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $posResult['position_name'] }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Total Suara Masuk: {{ number_format($posResult['total_votes']) }}</p>
                        </div>
                        @if ($posResult['abstain_votes'] > 0)
                            <span class="text-xs font-medium text-slate-500">Abstain/Golput: {{ $posResult['abstain_votes'] }} ({{ $posResult['abstain_percentage'] }}%)</span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @foreach ($posResult['candidates'] as $cand)
                            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $cand['number'] ?? '#' }}. {{ $cand['name'] }}</span>
                                        @if ($cand['slogan'])
                                            <span class="text-slate-400 italic">"{{ $cand['slogan'] }}"</span>
                                        @endif
                                    </div>
                                    <div class="font-bold text-slate-800">
                                        {{ number_format($cand['votes']) }} Suara <span class="text-blue-600 ml-1">({{ $cand['percentage'] }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full bg-blue-600 transition-all duration-500" data-progress-width="{{ $cand['percentage'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @else
        <div class="p-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
            <i class="fa-solid fa-chart-column text-4xl mb-3 text-slate-300 block"></i>
            Tidak ada data pemilihan aktif yang dipilih.
        </div>
    @endif
</div>
