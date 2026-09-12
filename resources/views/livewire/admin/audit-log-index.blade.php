<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Audit Trail & Log Keamanan</h1>
            <p class="text-xs text-slate-500 mt-1">Catatan aktivitas sistem yang tidak dapat diubah (immutable) demi transparansi dan kepatuhan privasi.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
            <i class="fa-solid fa-lock text-slate-500"></i>
            <span>Anonimitas Pilihan Dijamin</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aksi, pengguna, IP address..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white transition" />
        </div>

        <div class="w-full sm:w-auto">
            <select wire:model.live="actionFilter" class="w-full sm:w-56 px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-slate-50 focus:bg-white outline-none">
                <option value="">Semua Kategori Aksi</option>
                @foreach ($distinctActions as $act)
                    <option value="{{ $act }}">{{ $act }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="table-header">
                    <tr>
                        <th class="py-3.5 px-5">Waktu Kejadian</th>
                        <th class="py-3.5 px-5">Pengguna Terkait</th>
                        <th class="py-3.5 px-5">Aksi / Aktivitas</th>
                        <th class="py-3.5 px-5">Alamat IP & User Agent</th>
                        <th class="py-3.5 px-5">Metadata Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5 text-slate-500 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i:s') }} {{ \App\Enums\IndonesianTimezone::currentAbbr() }}</div>
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($log->user)
                                    <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">Sistem / Anonim</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold {{ str_contains($log->action, 'BALLOT') ? 'bg-emerald-50 text-emerald-700' : (str_contains($log->action, 'CREATE') ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-mono text-slate-700">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $log->user_agent ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-[11px] text-slate-600">
                                @if ($log->metadata)
                                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100 font-mono text-[10px] max-w-xs truncate">
                                        {{ json_encode($log->metadata) }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <i class="fa-solid fa-shield-halved text-4xl mb-2 text-slate-300 block"></i>
                                Belum ada log aktivitas tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
