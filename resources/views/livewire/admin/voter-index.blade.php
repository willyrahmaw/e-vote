<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Pemilih Tetap (DPT)</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data pemilih sah, status hak suara, dan import DPT massal via CSV/Excel.</p>
        </div>
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
            <button wire:click="$set('isAssignModalOpen', true)" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Manual</span>
            </button>
            <button wire:click="$set('isImportModalOpen', true)" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition cursor-pointer">
                <i class="fa-solid fa-file-csv text-xs"></i>
                <span>Import CSV DPT</span>
            </button>
        </div>
    </div>

    <!-- Election Selector & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Election Selector -->
            <select wire:model.live="selectedElectionId" class="w-full sm:w-72 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white outline-none">
                <option value="">Pilih Sesi Pemilihan</option>
                @foreach ($elections as $elec)
                    <option value="{{ $elec->id }}">{{ $elec->name }} ({{ $elec->status->label() }})</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select wire:model.live="filterStatus" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-slate-50 focus:bg-white outline-none">
                <option value="">Semua Pemilih</option>
                <option value="voted">Sudah Memilih</option>
                <option value="not_voted">Belum Memilih</option>
                <option value="eligible">Hak Suara Aktif</option>
                <option value="ineligible">Hak Suara Nonaktif</option>
            </select>
        </div>

        <!-- Search input -->
        <div class="w-full md:w-72 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, NIM..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white transition" />
        </div>
    </div>

    <!-- Voters Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        @if ($currentElection)
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                <div class="font-bold text-slate-800">
                    {{ $currentElection->name }}
                </div>
                <div class="flex items-center gap-3 text-slate-500 font-medium">
                    <span>Total DPT: <strong class="text-slate-900">{{ $currentElection->voters()->count() }}</strong></span>
                    <span>&bull;</span>
                    <span class="text-emerald-600 font-semibold">Sudah Voting: {{ $currentElection->voters()->where('has_voted', true)->count() }}</span>
                    <span>&bull;</span>
                    <span class="text-amber-600 font-semibold">Belum Voting: {{ $currentElection->voters()->where('has_voted', false)->count() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-5">Nama Pemilih</th>
                            <th class="py-3.5 px-5">Email &amp; Identitas</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Status Hak Suara</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Partisipasi Voting</th>
                            <th class="py-3.5 px-5 whitespace-nowrap">Waktu Voting</th>
                            <th class="py-3.5 px-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse ($voters as $voter)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900">{{ $voter->user->name }}</div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="text-slate-700">{{ $voter->user->email }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $voter->user->identifier ?? 'Tanpa ID' }}</div>
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <button 
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: '{{ $voter->is_eligible ? 'Cabut Hak Suara Pemilih?' : 'Aktifkan Hak Suara Pemilih?' }}',
                                                text: '{{ $voter->is_eligible ? 'Pemilih tidak akan dapat memberikan suara pada pemilihan ini.' : 'Pemilih akan diizinkan memberikan suara pada pemilihan ini.' }}',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '{{ $voter->is_eligible ? '#e11d48' : '#059669' }}',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: '{{ $voter->is_eligible ? 'Ya, Cabut Hak' : 'Ya, Aktifkan' }}',
                                                cancelButtonText: 'Batal',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.toggleEligibility('{{ $voter->id }}');
                                                }
                                            })
                                        "
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition cursor-pointer shadow-2xs {{ $voter->is_eligible ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200/80 hover:bg-rose-100' }}"
                                        title="Klik untuk ubah hak suara"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $voter->is_eligible ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $voter->is_eligible ? 'Berhak Memilih' : 'Hak Dicabut' }}</span>
                                    </button>
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    @if ($voter->has_voted)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Sudah Memilih
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200/80 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Belum Memilih
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-slate-500 text-[11px] whitespace-nowrap font-mono">
                                    {{ $voter->voted_at ? $voter->voted_at->format('d M Y H:i:s') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <button 
                                        type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'Hapus Pemilih dari DPT?',
                                                text: 'Pemilih {{ addslashes($voter->user->name) }} akan dihapus dari daftar DPT pemilihan ini.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#e11d48',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: '<i class=\'fa-solid fa-trash-can mr-1\'></i> Ya, Hapus',
                                                cancelButtonText: 'Batal',
                                                reverseButtons: true
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteVoter('{{ $voter->id }}');
                                                }
                                            })
                                        "
                                        class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" 
                                        title="Hapus dari DPT"
                                    >
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    <i class="fa-solid fa-id-card text-4xl mb-2 text-slate-300 block"></i>
                                    Tidak ada pemilih ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $voters->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400">
                <i class="fa-solid fa-vote-yea text-4xl mb-3 text-slate-300 block"></i>
                Silakan pilih sesi pemilihan di atas untuk melihat dan mengelola DPT.
            </div>
        @endif
    </div>

    <!-- Modal Import CSV -->
    @if ($isImportModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Import Massal DPT via CSV</h3>
                    <button wire:click="$set('isImportModalOpen', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="font-bold">Format Kolom Berkas CSV:</p>
                        <button type="button" wire:click="downloadTemplate" class="text-blue-700 hover:text-blue-800 font-bold underline flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-download text-[10px]"></i> Unduh Template CSV
                        </button>
                    </div>
                    <code class="block bg-white/80 p-2 rounded text-[11px] font-mono border border-blue-200 break-all">
                        Nama Lengkap, Alamat Email, NIM / NIK, Password (Opsional)
                    </code>
                    <p class="text-[11px] text-blue-800 leading-relaxed">
                        &bull; <strong>Password:</strong> Jika kolom password pada CSV kosong, akun pemilih otomatis diberikan default password: <code class="bg-white px-1.5 py-0.5 rounded font-mono font-bold text-slate-800">password</code>.<br>
                        &bull; Pemilih nantinya dapat login ke sistem menggunakan <strong>Email atau NIM</strong> + password tersebut.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilih Berkas CSV</label>
                    <input wire:model="csvFile" type="file" accept=".csv,text/csv" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                </div>

                @if (! empty($importPreview))
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 flex items-center justify-between">
                            <span>Pratinjau Data ({{ count($importPreview) }} Baris Pertama)</span>
                            <span class="text-[10px] text-slate-500 font-normal">Nama &bull; Email &bull; NIM &bull; Password</span>
                        </div>
                        <div class="max-h-48 overflow-y-auto divide-y divide-slate-100 text-xs">
                            @foreach ($importPreview as $p)
                                <div class="px-3 py-2 flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold text-slate-800">{{ $p['name'] }}</span>
                                        <span class="text-slate-400 text-[11px] ml-1">({{ $p['identifier'] ?: 'Tanpa NIM' }})</span>
                                        <div class="text-[11px] text-slate-500">{{ $p['email'] }}</div>
                                    </div>
                                    <span class="font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded text-slate-700">
                                        Pass: {{ $p['password'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="$set('isImportModalOpen', false)" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="processImport" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs cursor-pointer">
                        Mulai Import Data
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Manual Add Voter -->
    @if ($isAssignModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Tambah Pemilih Baru ke DPT</h3>
                    <button wire:click="$set('isAssignModalOpen', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Pemilih <span class="text-rose-500">*</span></label>
                        <input wire:model="newVoterName" type="text" placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        @error('newVoterName') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input wire:model="newVoterEmail" type="email" placeholder="budi@example.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        @error('newVoterEmail') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Induk / NIM (Opsional)</label>
                        <input wire:model="newVoterIdentifier" type="text" placeholder="Contoh: 202600123" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi / Password Akun</label>
                        <input wire:model="newVoterPassword" type="text" placeholder="Kosongkan untuk default: password" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        <span class="text-[10px] text-slate-400 mt-1 block">
                            *Jika dikosongkan, akun otomatis memiliki password default: <strong class="text-slate-700 font-mono">password</strong>.
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="$set('isAssignModalOpen', false)" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="assignVoter" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs cursor-pointer">
                        Tambahkan Pemilih
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
