<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Sesi Pemilihan</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola status siklus hidup pemilihan (Draft, Terjadwal, Aktif, Selesai, Dibatalkan).</p>
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

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama pemilihan..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs outline-none bg-slate-50 focus:bg-white transition" />
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <select wire:model.live="statusFilter" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-slate-50 focus:bg-white outline-none">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Elections Grid / Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5">Nama &amp; Slug</th>
                        <th class="py-3.5 px-5">Model Voting</th>
                        <th class="py-3.5 px-5">Jadwal Pelaksanaan</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-5">DPT &amp; Suara</th>
                        <th class="py-3.5 px-5 text-right whitespace-nowrap">Kontrol &amp; Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse ($elections as $election)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $election->name }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $election->organization?->name ?? 'Independen' }}</div>
                            </td>
                            <td class="py-4 px-5">
                                @if ($election->candidate_groups_count > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 font-semibold text-[11px]">
                                        <i class="fa-solid fa-users-rectangle"></i> Paslon ({{ $election->candidate_groups_count }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        <i class="fa-solid fa-layer-group"></i> {{ $election->positions_count }} Posisi
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-slate-500">
                                <div>{{ $election->start_at->format('d M Y, H:i') }}</div>
                                <div class="text-[10px] text-slate-400">s/d {{ $election->end_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold shadow-2xs {{ $election->status->badgeClass() }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $election->status->dotClass() }}"></span>
                                    {{ $election->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                <div class="text-slate-800 font-bold">{{ number_format($election->ballots_count) }} <span class="text-slate-400 font-normal">/ {{ number_format($election->voters_count) }} DPT</span></div>
                            </td>
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Status Action Triggers -->
                                    @if ($election->isDraft())
                                        <button 
                                            type="button"
                                            x-data
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Publikasikan Pemilihan?',
                                                    text: 'Status pemilihan akan diubah menjadi Terjadwal (Published).',
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#2563eb',
                                                    cancelButtonColor: '#64748b',
                                                    confirmButtonText: '<i class=\'fa-solid fa-paper-plane mr-1\'></i> Ya, Publish',
                                                    cancelButtonText: 'Batal',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.publish('{{ $election->id }}');
                                                    }
                                                })
                                            "
                                            class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition cursor-pointer" 
                                            title="Publikasikan Pemilihan"
                                        >
                                            <i class="fa-solid fa-paper-plane mr-1"></i> Publish
                                        </button>
                                    @endif

                                    @if ($election->isScheduled())
                                        <button 
                                            type="button"
                                            x-data
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Mulai Sesi Voting?',
                                                    text: 'Sesi pemilihan akan segera dibuka dan pemilih yang terdaftar di DPT dapat langsung memberikan suara.',
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#059669',
                                                    cancelButtonColor: '#64748b',
                                                    confirmButtonText: '<i class=\'fa-solid fa-play mr-1\'></i> Ya, Buka Voting',
                                                    cancelButtonText: 'Batal',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.activate('{{ $election->id }}');
                                                    }
                                                })
                                            "
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs transition cursor-pointer" 
                                            title="Mulai Sesi Voting Sekarang"
                                        >
                                            <i class="fa-solid fa-play mr-1"></i> Aktifkan
                                        </button>
                                    @endif

                                    @if ($election->isActive())
                                        <button 
                                            type="button"
                                            x-data
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Tutup Sesi Pemilihan?',
                                                    text: 'Apakah Anda yakin ingin menyelesaikan sesi pemilihan ini? Pemilih tidak akan dapat memberikan suara lagi setelah sesi ditutup.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d97706',
                                                    cancelButtonColor: '#64748b',
                                                    confirmButtonText: '<i class=\'fa-solid fa-stop mr-1\'></i> Ya, Selesaikan Pemilihan',
                                                    cancelButtonText: 'Batal',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.end('{{ $election->id }}');
                                                    }
                                                })
                                            "
                                            class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-xs transition cursor-pointer" 
                                            title="Tutup Pemilihan"
                                        >
                                            <i class="fa-solid fa-stop mr-1"></i> Selesai
                                        </button>
                                    @endif

                                    <!-- Official Report / Berita Acara -->
                                    <a href="{{ route('admin.elections.report', ['election' => $election->id]) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Cetak Berita Acara Resmi (PDF/Print)">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </a>

                                    <!-- Live Monitor -->
                                    <a href="{{ route('admin.live-voting', ['electionId' => $election->id]) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Live Monitor Admin">
                                        <i class="fa-solid fa-chart-column"></i>
                                    </a>

                                    <!-- Standalone Big Screen -->
                                    <a href="{{ route('screen.live', ['election' => $election->id]) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Buka Layar Monitor (Tanpa Menu)">
                                        <i class="fa-solid fa-desktop"></i>
                                    </a>

                                    <!-- Edit Wizard -->
                                    <a href="{{ route('admin.elections.wizard', ['id' => $election->id]) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" title="Edit Pengaturan">
                                        <i class="fa-solid fa-sliders"></i>
                                    </a>

                                    <!-- Duplicate -->
                                    <button wire:click="duplicate('{{ $election->id }}')" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition cursor-pointer" title="Duplikasi Pemilihan">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>

                                    <!-- Cancel / Delete -->
                                    @if (! $election->isCancelled() && ! $election->isEnded())
                                        <button 
                                            type="button"
                                            x-data
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Batalkan Pemilihan?',
                                                    text: 'Status pemilihan akan diubah menjadi Dibatalkan. Tindakan ini akan menghentikan seluruh proses pemilihan.',
                                                    icon: 'error',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#e11d48',
                                                    cancelButtonColor: '#64748b',
                                                    confirmButtonText: '<i class=\'fa-solid fa-ban mr-1\'></i> Ya, Batalkan',
                                                    cancelButtonText: 'Kembali',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.cancel('{{ $election->id }}');
                                                    }
                                                })
                                            "
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" 
                                            title="Batalkan Pemilihan"
                                        >
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @endif

                                    <button 
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'Hapus Pemilihan?',
                                                text: 'Apakah Anda yakin ingin menghapus data pemilihan ini? Data yang dihapus tidak dapat dipulihkan.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#e11d48',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: '<i class=\'fa-solid fa-trash mr-1\'></i> Ya, Hapus',
                                                cancelButtonText: 'Batal',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete('{{ $election->id }}');
                                                }
                                            })
                                        "
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" 
                                        title="Hapus"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-vote-yea text-4xl mb-2 text-slate-300 block"></i>
                                Tidak ada pemilihan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $elections->links() }}
        </div>
    </div>
</div>
