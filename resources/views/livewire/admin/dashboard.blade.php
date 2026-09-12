<div class="space-y-6">
    @if (auth()->user()->isUsingDefaultPassword())
        <!-- Security Hero Warning: Default Password Detected -->
        <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white shadow-lg shadow-red-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 relative overflow-hidden border border-red-400/30">
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-xl shrink-0 shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-black/20 text-white text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-red-300 animate-pulse"></span>
                            Peringatan Keamanan Administrator
                        </span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black tracking-tight text-white">Akun Admin Anda Masih Menggunakan Password Bawaan!</h2>
                    <p class="text-white/90 text-xs mt-0.5 max-w-2xl leading-relaxed">
                        Akun administrator Anda masih menggunakan kata sandi standar sistem (<code class="bg-black/25 px-1.5 py-0.5 rounded font-mono text-white text-xs">password</code>). Harap segera ubah kata sandi baru untuk mengamankan hak akses sistem e-voting.
                    </p>
                </div>
            </div>

            <div class="relative z-10 shrink-0 w-full md:w-auto">
                <a href="{{ route('admin.profile') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white text-slate-900 hover:bg-slate-50 font-black text-xs shadow-md transition active:scale-[0.98]">
                    <i class="fa-solid fa-key text-red-600 text-sm"></i>
                    <span>Ganti Password Admin</span>
                    <i class="fa-solid fa-arrow-right text-xs text-slate-400"></i>
                </a>
            </div>
        </div>
    @endif
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Ringkasan E-Voting</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau seluruh pemilihan, partisipasi pemilih, dan integritas surat suara secara realtime.</p>
        </div>
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('screen.live') }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-desktop text-amber-400 text-xs"></i>
                <span>Layar Monitor</span>
            </a>
            <a href="{{ route('admin.elections.wizard') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Buat Pemilihan Baru</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pemilihan</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_elections']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-vote-yea"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs text-emerald-600 font-medium">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold">{{ $stats['active_elections'] }} Aktif</span>
                <span class="text-slate-400">&bull;</span>
                <span class="text-slate-500">{{ $stats['scheduled_elections'] }} Terjadwal</span>
            </div>
        </div>

        <!-- Metric 2 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Partisipasi (Turnout)</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['turnout_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" data-progress-width="{{ min(100, (int) $stats['turnout_rate']) }}"></div>
                </div>
            </div>
        </div>

        <!-- Metric 3 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Suara Telah Masuk</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_votes_cast']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-500 font-medium">
                Dari total {{ number_format($stats['total_voters_registered']) }} hak pemilih terdaftar
            </div>
        </div>

        <!-- Metric 4 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Database Kandidat</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_candidates']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-slate-500 font-medium">
                Tersedia di seluruh organisasi
            </div>
        </div>
    </div>

    <!-- Recent Elections Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-sm">Daftar Pemilihan Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Sesi pemilihan aktif dan riwayat pemilihan sebelumnya</p>
            </div>
            <a href="{{ route('admin.elections.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>Lihat Semua</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="table-header">
                    <tr>
                        <th class="py-3.5 px-5">Nama Pemilihan</th>
                        <th class="py-3.5 px-5">Periode Waktu</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5">Pemilih Terdaftar</th>
                        <th class="py-3.5 px-5">Suara Masuk</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse ($recentElections as $election)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900">{{ $election->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $election->slug }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-slate-500">
                                <div>{{ $election->start_at->format('d M Y H:i') }}</div>
                                <div class="text-[10px] text-slate-400">s/d {{ $election->end_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $election->status->badgeClass() }}">
                                    {{ $election->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                {{ number_format($election->voters_count) }}
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-blue-600">
                                {{ number_format($election->ballots_count) }}
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
                                <a href="{{ route('admin.live-voting', ['electionId' => $election->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold transition" title="Live Monitor Admin">
                                    <i class="fa-solid fa-chart-pie mr-1"></i> Live
                                </a>
                                <a href="{{ route('screen.live', ['election' => $election->id]) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-900 text-amber-400 hover:bg-slate-800 font-semibold transition" title="Buka Layar Monitor (Tanpa Menu)">
                                    <i class="fa-solid fa-desktop"></i>
                                </a>
                                <a href="{{ route('admin.elections.wizard', ['id' => $election->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold transition" title="Edit Pemilihan">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada pemilihan yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
