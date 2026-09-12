<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header & Instructions Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                <i class="fa-solid fa-person-booth text-[10px]"></i> Sesi Bilik Suara Resmi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $election->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">
                {{ $election->instructions ?: 'Tentukan pilihan Anda secara cermat. Surat suara Anda akan dienkripsi tanpa identitas pemilih.' }}
            </p>
        </div>

        <!-- Dynamic Countdown Box (Isolated from Livewire DOM morphing) -->
        <div wire:ignore class="shrink-0 p-4 rounded-2xl bg-slate-900 text-white flex flex-col items-center justify-center min-w-[200px]"
            data-countdown
            data-start="{{ $election->start_at->toIso8601String() }}"
            data-end="{{ $election->end_at->toIso8601String() }}">
            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1" data-countdown-label>
                Sisa Waktu Memilih:
            </span>
            <div class="flex items-center gap-2 text-white font-mono">
                <div class="text-center"><span class="text-lg font-black text-blue-400" data-hours>00</span><span class="text-[8px] block text-slate-400">JAM</span></div>
                <span class="text-slate-500 font-bold">:</span>
                <div class="text-center"><span class="text-lg font-black text-blue-400" data-minutes>00</span><span class="text-[8px] block text-slate-400">MNT</span></div>
                <span class="text-slate-500 font-bold">:</span>
                <div class="text-center"><span class="text-lg font-black text-blue-400" data-seconds>00</span><span class="text-[8px] block text-slate-400">DTK</span></div>
            </div>
        </div>
    </div>

    <!-- Ballot Selection Area -->
    @if ($election->candidateGroups->isNotEmpty() && $election->positions->isEmpty())
        <!-- Paslon Selection Format -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-users-rectangle text-blue-600"></i>
                <span>Pilih Pasangan Calon (Paslon)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($election->candidateGroups as $group)
                    @php
                        $isSelected = ($selectedChoices['group']['group_id'] ?? null) === $group->id;
                    @endphp

                    <div class="candidate-card rounded-3xl border-2 p-6 flex flex-col justify-between space-y-6 bg-white {{ $isSelected ? 'selected border-blue-600' : 'border-slate-200 hover:border-slate-300' }}">
                        <div>
                            <!-- Number & Badge Header -->
                            <div class="flex items-center justify-between">
                                <span class="w-10 h-10 rounded-2xl {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-slate-900 text-white' }} font-black text-base flex items-center justify-center shadow-md">
                                    {{ $group->number }}
                                </span>
                                @if ($isSelected)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 bg-blue-100/70 px-3 py-1 rounded-full">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Dipilih
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-black text-slate-900 text-lg mt-4">{{ $group->name }}</h3>
                            <p class="text-xs text-blue-600 font-semibold italic mt-1">"{{ $group->slogan }}"</p>

                            <!-- Members (Duet) -->
                            <div class="grid grid-cols-2 gap-3 mt-5">
                                @foreach ($group->members as $member)
                                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center space-y-2">
                                        @if ($member->candidate->photo)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($member->candidate->photo, 'http') ? $member->candidate->photo : asset('storage/' . $member->candidate->photo) }}" alt="{{ $member->candidate->name }}" class="w-16 h-16 rounded-xl object-cover mx-auto border border-slate-200 shadow-xs" />
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-blue-100 text-blue-700 font-bold text-lg flex items-center justify-center mx-auto">
                                                {{ strtoupper(substr($member->candidate->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs truncate">{{ $member->candidate->name }}</p>
                                            <p class="text-[10px] text-slate-500 font-semibold uppercase">{{ $member->sort_order === 1 ? 'Calon Ketua' : 'Calon Wakil' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Visi & Misi Snippet -->
                            @if ($group->vision || $group->mission)
                                <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 space-y-2">
                                    @if ($group->vision)
                                        <div>
                                            <span class="font-bold text-[10px] uppercase tracking-wider text-blue-600 block">Visi:</span>
                                            <p class="line-clamp-2 text-slate-700 leading-relaxed">{{ $group->vision }}</p>
                                        </div>
                                    @endif
                                    @if ($group->mission)
                                        <div>
                                            <span class="font-bold text-[10px] uppercase tracking-wider text-emerald-600 block">Misi:</span>
                                            <p class="line-clamp-2 text-slate-600 leading-relaxed whitespace-pre-line">{{ $group->mission }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-3 text-center">
                                <button type="button" wire:click="openGroupDetail('{{ $group->id }}')" class="text-xs text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1 hover:underline cursor-pointer">
                                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    <span>Lihat Visi, Misi &amp; Profil Lengkap</span>
                                </button>
                            </div>
                        </div>

                        <!-- Select Action Button -->
                        <button type="button" wire:click="selectGroup('{{ $group->id }}')"
                            class="w-full py-3 px-4 rounded-2xl font-bold text-xs shadow-xs transition cursor-pointer {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 hover:bg-blue-50 text-slate-800 hover:text-blue-700' }}">
                            {{ $isSelected ? '✓ Pilihan Anda Saat Ini' : 'Pilih Paslon ' . $group->number }}
                        </button>
                    </div>
                @endforeach
            </div>

            @if ($election->allow_abstain)
                <div class="pt-4 text-center">
                    <button type="button" wire:click="abstainGroup"
                        class="px-6 py-2.5 rounded-full text-xs font-semibold transition cursor-pointer {{ ($selectedChoices['group']['is_abstain'] ?? false) ? 'bg-slate-800 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                        <i class="fa-solid fa-ban mr-1"></i>
                        {{ ($selectedChoices['group']['is_abstain'] ?? false) ? '✓ Anda Memilih Opsi Golput / Abstain' : 'Pilih Opsi Golput / Abstain (Surat Suara Kosong)' }}
                    </button>
                </div>
            @endif
        </div>
    @else
        <!-- Multi Position Selection Format -->
        <div class="space-y-8">
            @foreach ($election->positions as $position)
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">{{ $position->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $position->description ?: "Pilih {$position->min_choices} kandidat untuk posisi ini." }}
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                            {{ $position->is_required ? 'Wajib Dipilih' : 'Opsional' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        @foreach ($position->candidateEntries as $entry)
                            @php
                                $isSelected = ($selectedChoices[$position->id]['entry_id'] ?? null) === $entry->id;
                            @endphp

                            <div class="candidate-card p-5 rounded-2xl border-2 flex flex-col justify-between space-y-4 bg-white {{ $isSelected ? 'selected border-blue-600' : 'border-slate-200 hover:border-slate-300' }}">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                            {{ $entry->number ?? '#' }}
                                        </span>
                                        @if ($isSelected)
                                            <span class="text-[11px] font-bold text-blue-600"><i class="fa-solid fa-check mr-1"></i> Dipilih</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 mt-3">
                                        @if ($entry->candidate->photo)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($entry->candidate->photo, 'http') ? $entry->candidate->photo : asset('storage/' . $entry->candidate->photo) }}" alt="{{ $entry->candidate->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-xs shrink-0" />
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($entry->candidate->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $entry->candidate->name }}</h4>
                                            @if ($entry->slogan)
                                                <p class="text-[11px] text-blue-600 italic truncate">"{{ $entry->slogan }}"</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Visi & Misi Snippet -->
                                    @if ($entry->vision || $entry->mission)
                                        <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 space-y-1.5">
                                            @if ($entry->vision)
                                                <div>
                                                    <span class="font-bold text-[10px] uppercase tracking-wider text-blue-600 block">Visi:</span>
                                                    <p class="line-clamp-2 text-slate-700 leading-relaxed">{{ $entry->vision }}</p>
                                                </div>
                                            @endif
                                            @if ($entry->mission)
                                                <div>
                                                    <span class="font-bold text-[10px] uppercase tracking-wider text-emerald-600 block">Misi:</span>
                                                    <p class="line-clamp-2 text-slate-600 leading-relaxed whitespace-pre-line">{{ $entry->mission }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-2.5 text-center">
                                        <button type="button" wire:click="openEntryDetail('{{ $position->id }}', '{{ $entry->id }}')" class="text-xs text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1 hover:underline cursor-pointer">
                                            <i class="fa-solid fa-circle-info text-[11px]"></i>
                                            <span>Lihat Visi &amp; Profil Lengkap</span>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" wire:click="selectCandidateEntry('{{ $position->id }}', '{{ $entry->id }}')"
                                    class="w-full py-2.5 px-3 rounded-xl font-bold text-xs transition cursor-pointer {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700' }}">
                                    {{ $isSelected ? '✓ Pilihan Anda' : 'Pilih Kandidat' }}
                                </button>
                            </div>
                        @endforeach
                    </div>

                    @if ($election->allow_abstain)
                        <div class="pt-2 text-right">
                            <button type="button" wire:click="abstainPosition('{{ $position->id }}')"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg transition cursor-pointer {{ ($selectedChoices[$position->id]['is_abstain'] ?? false) ? 'bg-slate-800 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-ban mr-1"></i> Opsi Abstain untuk posisi ini
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Bottom Submit Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-4 z-20">
        <div class="flex items-center gap-3 text-xs text-slate-600">
            <i class="fa-solid fa-shield-halved text-emerald-600 text-lg"></i>
            <span>Pilihan Anda belum terkirim. Klik tombol untuk meninjau dan mengonfirmasi pilihan surat suara.</span>
        </div>

        <button type="button" wire:click="reviewBallot"
            class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/30 transition active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
            <span>Tinjau & Kirim Suara</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </div>

    <!-- Modal Detail Visi & Misi Lengkap -->
    @if ($previewDetail)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-black text-base flex items-center justify-center shadow-md">
                            {{ $previewDetail['number'] ?? '#' }}
                        </span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $previewDetail['title'] }}</h3>
                            @if (!empty($previewDetail['position']))
                                <span class="text-xs text-slate-500 font-semibold">{{ $previewDetail['position'] }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center cursor-pointer transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if (!empty($previewDetail['slogan']))
                    <div class="p-3.5 rounded-2xl bg-blue-50/80 border border-blue-100 text-blue-900 text-center font-medium italic text-xs">
                        "{{ $previewDetail['slogan'] }}"
                    </div>
                @endif

                <!-- Anggota / Profil Kandidat -->
                @if (!empty($previewDetail['members']))
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Profil Calon</h4>
                        <div class="grid grid-cols-1 {{ count($previewDetail['members']) > 1 ? 'sm:grid-cols-2' : '' }} gap-3">
                            @foreach ($previewDetail['members'] as $m)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-3.5">
                                    @if (!empty($m['photo']))
                                        <img src="{{ \Illuminate\Support\Str::startsWith($m['photo'], 'http') ? $m['photo'] : asset('storage/' . $m['photo']) }}" alt="{{ $m['name'] }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-xs shrink-0" />
                                    @else
                                        <div class="w-14 h-14 rounded-xl bg-blue-100 text-blue-700 font-bold text-lg flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($m['name'], 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h5 class="font-bold text-slate-900 text-sm leading-tight">{{ $m['name'] }}</h5>
                                        <p class="text-[11px] text-blue-600 font-semibold uppercase mt-0.5">{{ $m['role'] }}</p>
                                        @if (!empty($m['identifier']))
                                            <p class="text-[10px] text-slate-400 mt-0.5">NIM/ID: {{ $m['identifier'] }}</p>
                                        @endif
                                        @if (!empty($m['bio']))
                                            <p class="text-xs text-slate-600 mt-2 leading-relaxed bg-white p-2.5 rounded-xl border border-slate-100">{{ $m['bio'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Visi Section -->
                @if (!empty($previewDetail['vision']))
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Visi</h4>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed font-medium">
                            {{ $previewDetail['vision'] }}
                        </div>
                    </div>
                @endif

                <!-- Misi Section -->
                @if (!empty($previewDetail['mission']))
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Misi &amp; Program Kerja</h4>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $previewDetail['mission'] }}
                        </div>
                    </div>
                @endif

                <div class="pt-2 flex justify-end">
                    <button type="button" wire:click="closeDetailModal" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Review Ballot Confirmation -->
    @if ($isReviewModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5">
                <div class="text-center space-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-2">
                        <i class="fa-solid fa-envelope-circle-check"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Konfirmasi Pilihan Surat Suara</h3>
                    <p class="text-xs text-slate-500">Periksa kembali pilihan Anda. Pilihan yang sudah dikirim bersifat final dan tidak dapat diubah.</p>
                </div>

                <!-- Review Content Details -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                    @if (isset($selectedChoices['group']))
                        @php
                            $grpId = $selectedChoices['group']['group_id'] ?? null;
                            $isAbs = $selectedChoices['group']['is_abstain'] ?? false;
                            $selectedGroupModel = $grpId ? $election->candidateGroups->find($grpId) : null;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Paslon Pilihan:</span>
                            @if ($isAbs)
                                <strong class="text-slate-700">Golput / Abstain</strong>
                            @elseif ($selectedGroupModel)
                                <strong class="text-blue-700 font-bold">No. {{ $selectedGroupModel->number }} - {{ $selectedGroupModel->name }}</strong>
                            @endif
                        </div>
                    @else
                        @foreach ($election->positions as $pos)
                            @php
                                $cId = $selectedChoices[$pos->id]['entry_id'] ?? null;
                                $isAbs = $selectedChoices[$pos->id]['is_abstain'] ?? false;
                                $selectedEntry = $cId ? $pos->candidateEntries->find($cId) : null;
                            @endphp
                            <div class="flex items-center justify-between border-b border-slate-100 last:border-none pb-2 last:pb-0">
                                <span class="text-slate-500">{{ $pos->name }}:</span>
                                @if ($isAbs)
                                    <strong class="text-slate-700">Golput / Abstain</strong>
                                @elseif ($selectedEntry)
                                    <strong class="text-blue-700">{{ $selectedEntry->candidate->name }}</strong>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Legal Agreement Checkbox -->
                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-xs">
                    <input type="checkbox" wire:model="form.confirmed" class="w-4 h-4 rounded border-slate-300 text-blue-600 mt-0.5" />
                    <span class="text-slate-600">Saya menyatakan bahwa pilihan di atas dibuat secara sadar, tanpa paksaan, dan memahami bahwa suara tidak dapat diubah setelah dikirim.</span>
                </label>
                @error('form.confirmed') <span class="text-rose-500 text-xs block">{{ $message }}</span> @enderror

                <!-- Modal Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" wire:click="closeReviewModal" class="py-3 px-4 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        Kembali Ubah
                    </button>
                    <button type="button" wire:click="submit" class="py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/30 transition active:scale-[0.98] cursor-pointer">
                        Kirim Suara Sah
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
