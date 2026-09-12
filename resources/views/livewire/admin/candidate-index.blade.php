<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Database Kandidat & Profil</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data profil induk kandidat yang dapat digunakan lintas sesi pemilihan.</p>
        </div>
        <div class="w-full sm:w-auto">
            <button wire:click="openCreateModal" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition cursor-pointer">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Kandidat Baru</span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
        <div class="w-full sm:w-80 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, NIM/ID, email..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 text-xs outline-none bg-slate-50 focus:bg-white transition" />
        </div>
    </div>

    <!-- Candidate Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($candidates as $candidate)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-start gap-4">
                        @if ($candidate->photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($candidate->photo, 'http') ? $candidate->photo : asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-xs" />
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 font-bold text-lg flex items-center justify-center border border-blue-100 shrink-0">
                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-slate-900 text-sm truncate">{{ $candidate->name }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $candidate->identifier ?? 'Tanpa ID' }}</p>
                            @if ($candidate->email)
                                <p class="text-xs text-slate-500 truncate mt-0.5"><i class="fa-regular fa-envelope text-[10px] mr-1"></i> {{ $candidate->email }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($candidate->bio)
                        <p class="text-xs text-slate-600 mt-4 line-clamp-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $candidate->bio }}
                        </p>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        <span class="font-semibold text-slate-800">{{ $candidate->entries_count + $candidate->group_memberships_count }}</span> partisipasi pemilihan
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button wire:click="editCandidate('{{ $candidate->id }}')" class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition" title="Edit Data">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="deleteCandidate('{{ $candidate->id }}')" wire:confirm="Hapus data kandidat ini?" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                <i class="fa-solid fa-users text-4xl mb-2 text-slate-300 block"></i>
                Belum ada kandidat terdaftar.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $candidates->links() }}
    </div>

    <!-- Modal Create / Edit Candidate -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">
                        {{ $form->candidate ? 'Edit Data Kandidat' : 'Tambah Kandidat Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input wire:model="form.name" type="text" placeholder="Nama kandidat..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        @error('form.name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">NIM / Nomor Identitas</label>
                            <input wire:model="form.identifier" type="text" placeholder="e.g. NIM / NIP" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email</label>
                            <input wire:model="form.email" type="email" placeholder="email@domain.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Biografi Singkat</label>
                        <textarea wire:model="form.bio" rows="3" placeholder="Pengalaman organisasi, latar belakang pendidikan..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Foto Profil</label>
                        <input wire:model="photoUpload" type="file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('photoUpload') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition">
                            Simpan Kandidat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
