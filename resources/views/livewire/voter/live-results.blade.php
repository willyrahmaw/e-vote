<div wire:poll.4s class="space-y-8 max-w-5xl mx-auto">
    <!-- Header with Pulse Streaming Badge -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping mr-1.5"></span>
                    HASIL REALTIME
                </span>
                <span class="text-xs text-slate-400">Pembaruan otomatis tiap 4s</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $election->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">Perolehan suara terverifikasi dari seluruh Tempat Pemungutan Suara digital.</p>
        </div>

        <a href="{{ route('voter.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition shrink-0">
            <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Suara Masuk</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['total_ballots']) }}</p>
            <span class="text-xs text-slate-400 mt-1 block">Surat suara sah terenkripsi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tingkat Partisipasi (Turnout)</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['turnout_percentage'] }}%</p>
            <span class="text-xs text-slate-400 mt-1 block">{{ number_format($stats['voted_count']) }} dari {{ number_format($stats['total_voters']) }} DPT</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Sesi</p>
            <div class="mt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                    {{ $stats['status_label'] }}
                </span>
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Sinkronisasi: {{ $stats['updated_at'] }}</span>
        </div>
    </div>

    <!-- Results Display -->
    @if ($stats['results']['has_groups'])
        <!-- Paslon Display -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                Perolehan Suara Pasangan Calon (Paslon)
            </h2>

            @php
                $isTieLeader = count($stats['results']['group_results']) > 1 
                    && $stats['results']['group_results'][0]['votes'] === $stats['results']['group_results'][1]['votes'];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($stats['results']['group_results'] as $idx => $g)
                    @php
                        $isStrictLeader = ! $isTieLeader && $idx === 0 && $g['votes'] > 0;
                    @endphp
                    <div class="p-6 rounded-2xl border-2 {{ $isStrictLeader ? 'border-amber-400 bg-amber-50/20' : 'border-slate-200 bg-white' }} space-y-5 relative">
                        @if ($isStrictLeader)
                            <span class="absolute -top-3 right-4 px-3 py-0.5 rounded-full bg-amber-400 text-slate-900 font-bold text-[10px] shadow-xs flex items-center gap-1">
                                <i class="fa-solid fa-star text-[9px]"></i> PEROLEHAN SEMENTARA
                            </span>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-2xl bg-slate-900 text-white font-black text-sm flex items-center justify-center">
                                {{ $g['number'] ?? '#' }}
                            </span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-slate-900">{{ $g['percentage'] }}%</span>
                                <span class="text-xs text-slate-500 block">{{ number_format($g['votes']) }} Suara</span>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">{{ $g['name'] }}</h3>
                            @if (! empty($g['slogan']))
                                <p class="text-xs text-blue-600 italic mt-0.5">"{{ $g['slogan'] }}"</p>
                            @endif
                        </div>

                        <!-- Visi & Misi -->
                        @if (! empty($g['vision']) || ! empty($g['mission']))
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1.5">
                                @if (! empty($g['vision']))
                                    <p class="text-slate-700 text-[11px] leading-relaxed line-clamp-2"><span class="font-bold text-blue-600">Visi:</span> {{ $g['vision'] }}</p>
                                @endif
                                @if (! empty($g['mission']))
                                    <p class="text-slate-600 text-[11px] leading-relaxed line-clamp-2"><span class="font-bold text-emerald-600">Misi:</span> {{ $g['mission'] }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden">
                            <div class="h-3.5 rounded-full bg-blue-600 transition-all duration-500" data-progress-width="{{ $g['percentage'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Multi Position Display -->
        @foreach ($stats['results']['position_results'] as $posRes)
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">{{ $posRes['position_name'] }}</h2>
                    <span class="text-xs text-slate-500">Total: {{ number_format($posRes['total_votes']) }} Suara</span>
                </div>

                <div class="space-y-4">
                    @foreach ($posRes['candidates'] as $c)
                        <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-2.5">
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <div class="font-bold text-slate-900">
                                    {{ $c['number'] ?? '#' }}. {{ $c['name'] }}
                                </div>
                                <div class="font-bold text-slate-800">
                                    {{ number_format($c['votes']) }} Suara <span class="text-blue-600 ml-1">({{ $c['percentage'] }}%)</span>
                                </div>
                            </div>
                            @if (! empty($c['vision']) || ! empty($c['mission']))
                                <div class="p-2.5 rounded-xl bg-white border border-slate-100 text-xs space-y-1">
                                    @if (! empty($c['vision']))
                                        <p class="text-[11px] text-slate-700 leading-relaxed line-clamp-2"><span class="font-bold text-blue-600">Visi:</span> {{ $c['vision'] }}</p>
                                    @endif
                                    @if (! empty($c['mission']))
                                        <p class="text-[11px] text-slate-600 leading-relaxed line-clamp-2"><span class="font-bold text-emerald-600">Misi:</span> {{ $c['mission'] }}</p>
                                    @endif
                                </div>
                            @endif
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full bg-blue-600 transition-all duration-500" data-progress-width="{{ $c['percentage'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
