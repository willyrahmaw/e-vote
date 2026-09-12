<div class="space-y-6">
    <!-- Wizard Header & Progress Bar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                    {{ $election ? 'Edit Konfigurasi: ' . $election->name : 'Wizard Pembuatan Pemilihan' }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Langkah {{ $currentStep }} dari {{ $totalSteps }}: Panduan terstruktur pembuatan pemilihan profesional.</p>
            </div>
            <a href="{{ route('admin.elections.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <!-- Stepper Navigation -->
        <div class="overflow-x-auto pb-2">
            <div class="flex items-center gap-2 min-w-max text-xs font-semibold">
                @php
                    $steps = [
                        1 => '1. Informasi',
                        2 => '2. Model Voting',
                        3 => '3. Posisi Jabatan',
                        4 => '4. Kandidat / Paslon',
                        5 => '5. Visi & Misi',
                        6 => '6. Daftar Pemilih',
                        7 => '7. Pengaturan Hasil',
                        8 => '8. Review Ringkasan',
                        9 => '9. Publikasi',
                    ];
                @endphp

                @foreach ($steps as $stepNum => $stepTitle)
                    <button type="button" wire:click="goToStep({{ $stepNum }})"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl transition {{ $currentStep === $stepNum ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : ($currentStep > $stepNum ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200') }}">
                        @if ($currentStep > $stepNum)
                            <i class="fa-solid fa-check text-xs"></i>
                        @else
                            <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] {{ $currentStep === $stepNum ? 'bg-white text-blue-600 font-bold' : 'bg-slate-300 text-slate-700' }}">{{ $stepNum }}</span>
                        @endif
                        <span>{{ $stepTitle }}</span>
                    </button>
                    @if ($stepNum < count($steps))
                        <i class="fa-solid fa-chevron-right text-slate-300 text-[10px]"></i>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Step Content Container -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <!-- STEP 1: Informasi Dasar -->
        @if ($currentStep === 1)
            <div class="space-y-5 max-w-2xl">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Langkah 1: Informasi Dasar Pemilihan</h2>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Nama Pemilihan <span class="text-rose-500">*</span></label>
                    <input wire:model="form.name" type="text" placeholder="Contoh: Pemilihan Ketua OSIS 2026/2027"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none" />
                    @error('form.name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Organisasi Penyelenggara</label>
                    <select wire:model="form.organization_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none">
                        <option value="">Pilih Organisasi (Opsional)</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Pemilihan</label>
                    <textarea wire:model="form.description" rows="3" placeholder="Jelaskan tujuan dan latar belakang pemilihan ini..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Instruksi untuk Pemilih</label>
                    <textarea wire:model="form.instructions" rows="2" placeholder="Tata cara memilih bagi voter..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Waktu Mulai <span class="text-rose-500">*</span></label>
                        <input wire:model="form.start_at" type="datetime-local" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none" />
                        @error('form.start_at') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Waktu Selesai <span class="text-rose-500">*</span></label>
                        <input wire:model="form.end_at" type="datetime-local" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none" />
                        @error('form.end_at') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- STEP 2: Model Voting -->
        @if ($currentStep === 2)
            <div class="space-y-5 max-w-2xl">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Langkah 2: Tentukan Model Voting</h2>
                <p class="text-xs text-slate-500">Pilih format struktur pencalonan yang akan digunakan dalam pemilihan ini.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="p-5 rounded-2xl border-2 cursor-pointer transition {{ $electionModel === 'group' ? 'border-blue-600 bg-blue-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" wire:model.live="electionModel" value="group" class="hidden" />
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg mb-3">
                            <i class="fa-solid fa-users-rectangle"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Pasangan Calon (Paslon / Tim)</h3>
                        <p class="text-xs text-slate-500 mt-1">Format duet pimpinan seperti Capres-Cawapres, Ketua-Wakil Ketua BEM, atau kelompok tim.</p>
                    </label>

                    <label class="p-5 rounded-2xl border-2 cursor-pointer transition {{ $electionModel === 'position' ? 'border-blue-600 bg-blue-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                        <input type="radio" wire:model.live="electionModel" value="position" class="hidden" />
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg mb-3">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">Jabatan Mandiri (Multi-Posisi)</h3>
                        <p class="text-xs text-slate-500 mt-1">Pemilih menentukan pilihan per jabatan terpisah (e.g. Ketua, Sekretaris, Bendahara, Formatur).</p>
                    </label>
                </div>
            </div>
        @endif

        <!-- STEP 3: Posisi Jabatan -->
        @if ($currentStep === 3)
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Langkah 3: Konfigurasi Posisi Jabatan</h2>
                        <p class="text-xs text-slate-500">Tentukan jabatan yang diperebutkan beserta batas minimal dan maksimal pilihan surat suara.</p>
                    </div>
                </div>

                @if ($election)
                    <!-- Existing Positions List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($election->positions as $pos)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $pos->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Min: {{ $pos->min_choices }} pilihan &bull; Max: {{ $pos->max_choices }} pilihan &bull; {{ $pos->is_required ? 'Wajib Diisi' : 'Opsional' }}</p>
                                </div>
                                <button wire:click="deletePosition('{{ $pos->id }}')" class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus Posisi">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        @empty
                            <div class="col-span-2 py-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                Belum ada posisi jabatan ditambahkan.
                            </div>
                        @endforelse
                    </div>

                    <!-- Add Position Form -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tambah Posisi Jabatan Baru</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div class="sm:col-span-2">
                                <input wire:model="newPosition.name" type="text" placeholder="Nama Jabatan (e.g. Ketua Umum)"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                            </div>
                            <div>
                                <input wire:model="newPosition.min_choices" type="number" min="1" placeholder="Min Pilihan"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                            </div>
                            <div>
                                <input wire:model="newPosition.max_choices" type="number" min="1" placeholder="Max Pilihan"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input wire:model="newPosition.is_required" type="checkbox" class="rounded border-slate-300 text-blue-600" />
                                <span>Posisi ini wajib ditentukan oleh pemilih</span>
                            </label>
                            <button type="button" wire:click="addPosition" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Posisi
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500">Silakan selesaikan Langkah 1 terlebih dahulu.</p>
                @endif
            </div>
        @endif

        <!-- STEP 4: Kandidat / Paslon Setup -->
        @if ($currentStep === 4)
            <div class="space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Langkah 4: Kandidat & Pasangan Calon</h2>
                    <p class="text-xs text-slate-500">Daftarkan pasangan calon atau tetapkan kandidat ke masing-masing posisi jabatan.</p>
                </div>

                @if ($election)
                    @if ($electionModel === 'group')
                        <!-- Paslon List -->
                        <div class="space-y-4">
                            @forelse ($election->candidateGroups as $group)
                                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">{{ $group->number ?? '#' }}</span>
                                            <h4 class="font-bold text-slate-900 text-sm">{{ $group->name }}</h4>
                                        </div>
                                        <p class="text-xs text-blue-600 font-medium italic mt-1">"{{ $group->slogan }}"</p>
                                        <div class="flex items-center gap-3 text-xs text-slate-600 mt-2">
                                            @foreach ($group->members as $member)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200 text-[11px] font-semibold">
                                                    <i class="fa-solid fa-user text-slate-400 text-[10px]"></i>
                                                    {{ $member->candidate->name }} ({{ $member->sort_order === 1 ? 'Ketua' : 'Wakil' }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button wire:click="deleteCandidateGroup('{{ $group->id }}')" class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 transition self-end sm:self-center" title="Hapus Paslon">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs">
                                    Belum ada Pasangan Calon (Paslon) didaftarkan.
                                </div>
                            @endforelse

                            <!-- Add Paslon Form -->
                            <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftarkan Pasangan Calon (Paslon) Baru</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Paslon <span class="text-rose-500">*</span></label>
                                        <input wire:model="newGroup.name" type="text" placeholder="Nama Paslon (e.g. Ahmad & Budi)" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Urut</label>
                                        <input wire:model="newGroup.number" type="text" placeholder="e.g. 01" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Slogan / Tagline Paslon</label>
                                        <input wire:model="newGroup.slogan" type="text" placeholder="e.g. Bergerak Bersama untuk Perubahan" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ketua / Utama <span class="text-rose-500">*</span></label>
                                        <select wire:model="newGroup.leader_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500">
                                            <option value="">Pilih Kandidat Ketua</option>
                                            @foreach ($candidates as $cand)
                                                <option value="{{ $cand->id }}">{{ $cand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Wakil / Pendamping</label>
                                        <select wire:model="newGroup.vice_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500">
                                            <option value="">Pilih Kandidat Wakil (Opsional)</option>
                                            @foreach ($candidates as $cand)
                                                <option value="{{ $cand->id }}">{{ $cand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Visi Utama Paslon</label>
                                        <textarea wire:model="newGroup.vision" rows="2" placeholder="Tuliskan visi utama paslon..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500"></textarea>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Misi / Program Kerja</label>
                                        <textarea wire:model="newGroup.mission" rows="3" placeholder="Tuliskan poin-poin misi / program kerja..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500"></textarea>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="button" wire:click="addCandidateGroup" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer">
                                        <i class="fa-solid fa-plus mr-1"></i> Daftarkan Paslon
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Individual Entries per Position -->
                        <div class="space-y-4">
                            @forelse ($election->candidateEntries as $entry)
                                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold text-[10px] mr-2">No. {{ $entry->number ?? '-' }}</span>
                                        <span class="font-bold text-slate-900 text-sm">{{ $entry->candidate->name }}</span>
                                        <span class="text-xs text-slate-500 ml-2">({{ $entry->position->name }})</span>
                                        @if ($entry->slogan)
                                            <p class="text-xs text-blue-600 italic mt-0.5">"{{ $entry->slogan }}"</p>
                                        @endif
                                    </div>
                                    <button wire:click="deleteCandidateEntry('{{ $entry->id }}')" class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs">
                                    Belum ada kandidat yang ditautkan ke posisi jabatan.
                                </div>
                            @endforelse

                            <!-- Add Individual Entry Form -->
                            <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Tautkan Kandidat ke Posisi</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Posisi Jabatan <span class="text-rose-500">*</span></label>
                                        <select wire:model="newEntry.position_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500">
                                            <option value="">Pilih Posisi Jabatan</option>
                                            @foreach ($election->positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kandidat <span class="text-rose-500">*</span></label>
                                        <select wire:model="newEntry.candidate_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500">
                                            <option value="">Pilih Kandidat</option>
                                            @foreach ($candidates as $cand)
                                                <option value="{{ $cand->id }}">{{ $cand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Urut</label>
                                        <input wire:model="newEntry.number" type="text" placeholder="e.g. 01" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Slogan / Tagline</label>
                                        <input wire:model="newEntry.slogan" type="text" placeholder="Slogan kandidat..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Visi</label>
                                        <textarea wire:model="newEntry.vision" rows="2" placeholder="Tuliskan visi kandidat..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500"></textarea>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Misi</label>
                                        <textarea wire:model="newEntry.mission" rows="3" placeholder="Tuliskan misi kandidat..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500"></textarea>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="button" wire:click="addCandidateEntry" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer">
                                        <i class="fa-solid fa-plus mr-1"></i> Tautkan Kandidat
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <!-- STEP 5: Visi & Misi Interactive Editor -->
        @if ($currentStep === 5)
            <div class="space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Langkah 5: Kelola Visi &amp; Misi Kandidat</h2>
                    <p class="text-xs text-slate-500">Tuliskan visi, misi, dan slogan untuk masing-masing paslon/kandidat. Data ini akan ditampilkan langsung kepada pemilih di bilik suara.</p>
                </div>

                @if ($election)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @if ($electionModel === 'group')
                            @forelse ($election->candidateGroups as $grp)
                                <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-xs space-y-4">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">{{ $grp->number }}</span>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm">{{ $grp->name }}</h4>
                                                <p class="text-[10px] text-slate-500">
                                                    @foreach ($grp->members as $m)
                                                        {{ $m->candidate->name }}{{ ! $loop->last ? ' & ' : '' }}
                                                    @endforeach
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Slogan / Tagline:</label>
                                            <input 
                                                wire:model="editingSlogan.{{ $grp->id }}" 
                                                type="text" 
                                                placeholder="e.g. Bergerak Bersama untuk Perubahan"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Visi Utama:</label>
                                            <textarea 
                                                wire:model="editingVision.{{ $grp->id }}" 
                                                rows="2" 
                                                placeholder="Tuliskan visi utama paslon ini..."
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            ></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Misi &amp; Program Kerja:</label>
                                            <textarea 
                                                wire:model="editingMission.{{ $grp->id }}" 
                                                rows="4" 
                                                placeholder="Tuliskan poin-poin misi (bisa beberapa baris)..."
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="pt-2 text-right">
                                        <button 
                                            type="button" 
                                            wire:click="saveVisionMission('{{ $grp->id }}')" 
                                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer shadow-xs inline-flex items-center gap-1.5"
                                        >
                                            <i class="fa-solid fa-floppy-disk text-[10px]"></i>
                                            <span>Simpan Visi &amp; Misi Paslon {{ $grp->number }}</span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 py-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                                    Belum ada Paslon terdaftar. Silakan tambahkan paslon di Langkah 4 terlebih dahulu.
                                </div>
                            @endforelse
                        @else
                            @forelse ($election->candidateEntries as $ent)
                                <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-xs space-y-4">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px] mr-1.5">No. {{ $ent->number ?? '#' }}</span>
                                            <span class="font-bold text-slate-900 text-sm">{{ $ent->candidate->name }}</span>
                                            <span class="text-xs text-slate-500 ml-1">({{ $ent->position->name }})</span>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Slogan / Tagline:</label>
                                            <input 
                                                wire:model="editingSlogan.{{ $ent->id }}" 
                                                type="text" 
                                                placeholder="Slogan kandidat..."
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Visi:</label>
                                            <textarea 
                                                wire:model="editingVision.{{ $ent->id }}" 
                                                rows="2" 
                                                placeholder="Tuliskan visi kandidat..."
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            ></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Misi &amp; Program Kerja:</label>
                                            <textarea 
                                                wire:model="editingMission.{{ $ent->id }}" 
                                                rows="4" 
                                                placeholder="Tuliskan poin-poin misi kandidat..."
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="pt-2 text-right">
                                        <button 
                                            type="button" 
                                            wire:click="saveVisionMission('{{ $ent->id }}')" 
                                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition cursor-pointer shadow-xs inline-flex items-center gap-1.5"
                                        >
                                            <i class="fa-solid fa-floppy-disk text-[10px]"></i>
                                            <span>Simpan Visi &amp; Misi</span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 py-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                                    Belum ada kandidat yang ditautkan ke posisi jabatan.
                                </div>
                            @endforelse
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- STEP 6: Daftar Pemilih Tetap (DPT) -->
        @if ($currentStep === 6)
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Langkah 6: Daftar Pemilih Tetap (DPT)</h2>
                        <p class="text-xs text-slate-500">Tentukan pengguna yang berhak memberikan suara dalam sesi pemilihan ini.</p>
                    </div>
                    <button type="button" wire:click="addAllVoters" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition">
                        <i class="fa-solid fa-users-medical mr-1"></i> Daftarkan Semua Voter Sistem
                    </button>
                </div>

                @if ($election)
                    <!-- Quick Add Single Voter -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center gap-3">
                        <select wire:model="selectedVoterUserId" class="flex-1 w-full px-3 py-2 rounded-xl border border-slate-300 text-xs outline-none bg-white">
                            <option value="">Pilih Pengguna dari Sistem</option>
                            @foreach ($availableUsers as $vUser)
                                <option value="{{ $vUser->id }}">{{ $vUser->name }} ({{ $vUser->email }})</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="addVoter" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition">
                            <i class="fa-solid fa-plus mr-1"></i> Tambahkan
                        </button>
                    </div>

                    <!-- Voter List -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-700 flex justify-between items-center">
                            <span>Total Terdaftar: {{ $election->voters->count() }} Pemilih</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                            @forelse ($election->voters as $voter)
                                <div class="px-4 py-3 flex items-center justify-between text-xs hover:bg-slate-50">
                                    <div>
                                        <span class="font-bold text-slate-900">{{ $voter->user->name }}</span>
                                        <span class="text-slate-400 ml-2">{{ $voter->user->email }}</span>
                                    </div>
                                    <button wire:click="deleteVoter('{{ $voter->id }}')" class="text-rose-500 hover:text-rose-700 p-1">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs">
                                    Belum ada pemilih yang didaftarkan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- STEP 7: Pengaturan Hasil & Keterbukaan -->
        @if ($currentStep === 7)
            <div class="space-y-6 max-w-2xl">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Langkah 7: Pengaturan Hasil & Privasi</h2>
                    <p class="text-xs text-slate-500">Konfigurasikan transparansi perolehan suara dan opsi surat suara.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Keterbukaan Hasil Perolehan Suara</label>
                    <select wire:model="form.result_visibility" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-sm outline-none">
                        @foreach ($visibilities as $vis)
                            <option value="{{ $vis->value }}">{{ $vis->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                        <input wire:model="form.is_live_result_enabled" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600" />
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Aktifkan Live Result Streaming</span>
                            <span class="text-[11px] text-slate-500 block">Menyediakan halaman polling realtime dengan auto-refresh 3-5 detik.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                        <input wire:model="form.allow_abstain" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600" />
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Izinkan Opsi Golput / Abstain</span>
                            <span class="text-[11px] text-slate-500 block">Pemilih dapat secara sah memilih opsi tidak memilih kandidat manapun.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                        <input wire:model="form.is_public" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600" />
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Publikasikan ke Halaman Umum</span>
                            <span class="text-[11px] text-slate-500 block">Dapat dilihat oleh semua akun yang login.</span>
                        </div>
                    </label>
                </div>
            </div>
        @endif

        <!-- STEP 8: Review Ringkasan -->
        @if ($currentStep === 8)
            <div class="space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-bold text-slate-900">Langkah 8: Review & Verifikasi Data Pemilihan</h2>
                    <p class="text-xs text-slate-500">Periksa seluruh detail konfigurasi sebelum mempublikasikan sesi pemilihan.</p>
                </div>

                @if ($election)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-200 pb-2">Informasi Umum</h4>
                            <div><span class="text-slate-500">Nama:</span> <strong class="text-slate-900">{{ $election->name }}</strong></div>
                            <div><span class="text-slate-500">Jadwal:</span> <strong class="text-slate-900">{{ $election->start_at->format('d M Y, H:i') }} s/d {{ $election->end_at->format('d M Y, H:i') }}</strong></div>
                            <div><span class="text-slate-500">Status Saat Ini:</span> <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $election->status->badgeClass() }}">{{ $election->status->label() }}</span></div>
                            <div><span class="text-slate-500">Visibilitas Hasil:</span> <strong class="text-slate-900">{{ $election->result_visibility->label() }}</strong></div>
                        </div>

                        <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
                            <h4 class="font-bold text-slate-900 text-sm border-b border-slate-200 pb-2">Kelengkapan Konten</h4>
                            <div><span class="text-slate-500">Model Voting:</span> <strong class="text-slate-900">{{ $electionModel === 'group' ? 'Paslon / Tim' : 'Multi-Jabatan' }}</strong></div>
                            <div><span class="text-slate-500">Total Paslon / Posisi:</span> <strong class="text-slate-900">{{ $electionModel === 'group' ? $election->candidateGroups->count() : $election->positions->count() }}</strong></div>
                            <div><span class="text-slate-500">Total DPT Terdaftar:</span> <strong class="text-slate-900">{{ $election->voters->count() }} Pemilih</strong></div>
                            <div><span class="text-slate-500">Opsi Abstain:</span> <strong class="text-slate-900">{{ $election->allow_abstain ? 'Diizinkan' : 'Tidak Diizinkan' }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- STEP 9: Publikasi -->
        @if ($currentStep === 9)
            <div class="space-y-6 max-w-xl text-center py-6 mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Siap Publikasikan Pemilihan?</h2>
                    <p class="text-xs text-slate-500 mt-1">Pemilihan akan beralih status menjadi <strong>Terjadwal</strong> atau <strong>Aktif</strong> sesuai waktu mulai yang ditetapkan.</p>
                </div>

                @if ($election)
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs text-left">
                        <div class="font-bold flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Catatan Keamanan:</span>
                        </div>
                        <p>Setelah suara pertama masuk, struktur surat suara tidak dapat diubah kembali untuk menjamin integritas data voting.</p>
                    </div>

                    <button type="button" wire:click="publish" class="w-full py-3 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/30 transition active:scale-[0.99] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Publikasikan Sesi Pemilihan Sekarang</span>
                    </button>
                @endif
            </div>
        @endif

        <!-- Wizard Navigation Footer -->
        <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-between">
            <button type="button" wire:click="previousStep" {{ $currentStep === 1 ? 'disabled' : '' }}
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Sebelumnya
            </button>

            @if ($currentStep < $totalSteps)
                <button type="button" wire:click="nextStep"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <span>Lanjutkan</span>
                    <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            @endif
        </div>
    </div>
</div>
