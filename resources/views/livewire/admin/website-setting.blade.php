<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 shadow-xs">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pengaturan Website</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola identitas sistem, informasi institusi, dan preferensi modul e-voting.</p>
            </div>
        </div>
    </div>

    @if (session()->has('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
        <button 
            type="button" 
            wire:click="setTab('branding')"
            class="px-4 py-3 rounded-t-xl text-xs font-bold transition flex items-center gap-2 border-b-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'branding' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}"
        >
            <i class="fa-solid fa-building text-sm"></i>
            <span>Identitas &amp; Branding</span>
        </button>

        <button 
            type="button" 
            wire:click="setTab('contact')"
            class="px-4 py-3 rounded-t-xl text-xs font-bold transition flex items-center gap-2 border-b-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'contact' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}"
        >
            <i class="fa-solid fa-address-book text-sm"></i>
            <span>Kontak &amp; Alamat</span>
        </button>

        <button 
            type="button" 
            wire:click="setTab('features')"
            class="px-4 py-3 rounded-t-xl text-xs font-bold transition flex items-center gap-2 border-b-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'features' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-100/60' }}"
        >
            <i class="fa-solid fa-shield-halved text-sm"></i>
            <span>Fitur &amp; Keamanan</span>
        </button>
    </div>

    <!-- Form Sections -->
    <form wire:submit.prevent="save">
        <!-- Tab 1: Identitas & Branding -->
        @if ($activeTab === 'branding')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                    <div>
                        <h2 class="font-bold text-slate-900 text-base">Identitas Aplikasi &amp; Lembaga</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi utama yang tampil pada header, portal pemilih, dan laporan hasil pemilihan.</p>
                    </div>

                    <!-- Logo Brand & Favicon Upload Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Logo Brand Web -->
                        <div class="p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">
                                    <i class="fa-solid fa-image text-blue-600 mr-1"></i> Logo Brand Web
                                </label>
                                @if ($app_logo || $logoUpload)
                                    <button 
                                        type="button" 
                                        wire:click="deleteLogo" 
                                        wire:confirm="Yakin ingin menghapus logo brand web?"
                                        class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer"
                                    >
                                        Hapus Logo
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1.5 shrink-0 overflow-hidden shadow-2xs">
                                    @if ($logoUpload)
                                        <img src="{{ $logoUpload->temporaryUrl() }}" alt="Preview Logo" class="w-full h-full object-contain">
                                    @elseif ($app_logo)
                                        <img src="{{ asset('storage/' . $app_logo) }}" alt="Logo Brand" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                                            <i class="fa-solid fa-check-to-slot"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input 
                                        type="file" 
                                        wire:model="logoUpload" 
                                        accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                        id="logoUpload"
                                        class="text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-full"
                                    />
                                    <p class="text-[10px] text-slate-400 mt-1">Format: PNG, JPG, SVG, WebP (Maks. 2MB)</p>
                                </div>
                            </div>
                            @error('logoUpload')
                                <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Favicon Web -->
                        <div class="p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">
                                    <i class="fa-solid fa-icons text-amber-500 mr-1"></i> Favicon / Icon Tab Web
                                </label>
                                @if ($app_favicon || $faviconUpload)
                                    <button 
                                        type="button" 
                                        wire:click="deleteFavicon" 
                                        wire:confirm="Yakin ingin menghapus favicon?"
                                        class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer"
                                    >
                                        Hapus Favicon
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-2 shrink-0 overflow-hidden shadow-2xs">
                                    @if ($faviconUpload)
                                        <img src="{{ $faviconUpload->temporaryUrl() }}" alt="Preview Favicon" class="w-full h-full object-contain">
                                    @elseif ($app_favicon)
                                        <img src="{{ asset('storage/' . $app_favicon) }}" alt="Favicon" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                                            <i class="fa-solid fa-check-to-slot"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input 
                                        type="file" 
                                        wire:model="faviconUpload" 
                                        accept="image/x-icon,image/png,image/svg+xml,image/jpeg"
                                        id="faviconUpload"
                                        class="text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer w-full"
                                    />
                                    <p class="text-[10px] text-slate-400 mt-1">Format: ICO, PNG, SVG (Maks. 1MB)</p>
                                </div>
                            </div>
                            @error('faviconUpload')
                                <p class="text-rose-500 text-[11px]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label for="app_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Aplikasi / Sistem <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="app_name" 
                                wire:model="app_name"
                                placeholder="Contoh: E-Voting Terpadu"
                                class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('app_name') ? 'border-rose-400' : 'border-slate-300 focus:border-blue-500' }} text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                            />
                            @error('app_name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="institution_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Lembaga / Penyelenggara <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="institution_name" 
                                wire:model="institution_name"
                                placeholder="Contoh: KPU Mahasiswa Universitas..."
                                class="w-full px-3.5 py-2.5 rounded-xl border {{ $errors->has('institution_name') ? 'border-rose-400' : 'border-slate-300 focus:border-blue-500' }} text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                            />
                            @error('institution_name')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="app_tagline" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tagline / Slogan
                        </label>
                        <input 
                            type="text" 
                            id="app_tagline" 
                            wire:model="app_tagline"
                            placeholder="Contoh: Jujur, Adil, Terbuka, dan Terverifikasi"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                        />
                    </div>

                    <div>
                        <label for="app_description" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Deskripsi Singkat Sistem
                        </label>
                        <textarea 
                            id="app_description" 
                            wire:model="app_description"
                            rows="3"
                            placeholder="Tuliskan gambaran ringkas mengenai sistem e-voting ini..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                        ></textarea>
                    </div>

                    <div>
                        <label for="footer_copyright" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Teks Hak Cipta / Footer
                        </label>
                        <input 
                            type="text" 
                            id="footer_copyright" 
                            wire:model="footer_copyright"
                            placeholder="Contoh: Sistem E-Voting Terdesentralisasi & Anonim."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                        />
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm pb-3 border-b border-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-eye text-blue-600"></i>
                            Pratinjau Branding
                        </h3>

                        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-br from-blue-700 to-indigo-800 text-white shadow-md">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white text-base overflow-hidden p-1 shrink-0">
                                    @if ($logoUpload)
                                        <img src="{{ $logoUpload->temporaryUrl() }}" alt="Logo" class="w-full h-full object-contain">
                                    @elseif ($app_logo)
                                        <img src="{{ asset('storage/' . $app_logo) }}" alt="Logo" class="w-full h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-check-to-slot"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm leading-tight">{{ $app_name ?: 'E-Voting' }}</h4>
                                    <p class="text-[10px] text-blue-200 truncate max-w-[180px]">{{ $institution_name ?: 'Nama Lembaga' }}</p>
                                </div>
                            </div>
                            <p class="text-[11px] text-blue-100 italic leading-relaxed">
                                "{{ $app_tagline ?: 'Slogan sistem pemilihan' }}"
                            </p>
                        </div>

                        <!-- Mini Tab Bar Preview -->
                        <div class="mt-4 p-3 rounded-xl bg-slate-900 text-white flex items-center gap-2 text-xs">
                            <div class="w-5 h-5 rounded-md bg-white/10 flex items-center justify-center overflow-hidden p-0.5 shrink-0">
                                @if ($faviconUpload)
                                    <img src="{{ $faviconUpload->temporaryUrl() }}" alt="Favicon" class="w-full h-full object-contain">
                                @elseif ($app_favicon)
                                    <img src="{{ asset('storage/' . $app_favicon) }}" alt="Favicon" class="w-full h-full object-contain">
                                @else
                                    <i class="fa-solid fa-check-to-slot text-[9px] text-blue-400"></i>
                                @endif
                            </div>
                            <span class="text-[11px] text-slate-300 font-medium truncate">{{ $app_name ?: 'E-Voting' }} - Browser Tab</span>
                        </div>

                        <div class="mt-4 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                            <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Perubahan logo brand dan icon tab (favicon) akan diterapkan di seluruh halaman panel admin, portal pemilih, dan halaman login.
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <span class="text-[11px] text-slate-400 font-medium">Status: Aktif</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab 2: Kontak & Alamat -->
        @if ($activeTab === 'contact')
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs space-y-5 max-w-3xl">
                <div>
                    <h2 class="font-bold text-slate-900 text-base">Informasi Kontak &amp; Bantuan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kontak yang dapat dihubungi oleh pemilih jika mengalami kendala login atau DPT.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="contact_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email Bantuan / Helpdesk
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </span>
                            <input 
                                type="email" 
                                id="contact_email" 
                                wire:model="contact_email"
                                placeholder="support@lembaga.ac.id"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border {{ $errors->has('contact_email') ? 'border-rose-400' : 'border-slate-300 focus:border-blue-500' }} text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                            />
                        </div>
                        @error('contact_email')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor WhatsApp / Hotline
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </span>
                            <input 
                                type="text" 
                                id="contact_phone" 
                                wire:model="contact_phone"
                                placeholder="+62 812-3456-7890"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                            />
                        </div>
                    </div>
                </div>

                <div>
                    <label for="institution_address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Kantor / Sekretariat Panitia
                    </label>
                    <textarea 
                        id="institution_address" 
                        wire:model="institution_address"
                        rows="3"
                        placeholder="Tuliskan alamat sekretariat atau kampus penyelenggara pemilihan..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                    ></textarea>
                </div>
            </div>
        @endif

        <!-- Tab 3: Fitur & Keamanan E-Voting -->
        @if ($activeTab === 'features')
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs space-y-6 max-w-3xl">
                <div>
                    <h2 class="font-bold text-slate-900 text-base">Fitur &amp; Kebijakan Sistem E-Voting</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Atur keterbukaan monitor hasil pemilihan dan verifikasi surat suara.</p>
                </div>

                <div class="space-y-4 pt-1">
                    <!-- Toggle 1: Public Live Screen -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-desktop"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Layar Monitor Publik (Kiosk / Quick Count)</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Izinkan akses halaman layar monitor besar tanpa menu (<code class="bg-slate-200/60 px-1 py-0.5 rounded font-mono text-[10px]">/screen</code>) untuk ditampilkan pada proyektor / monitor umum.
                                </p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="enable_public_monitor" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Toggle 2: Ballot Verification -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 flex items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Verifikasi Surat Suara Independen</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Izinkan pemilih memvalidasi receipt token surat suara anonim mereka secara publik (<code class="bg-slate-200/60 px-1 py-0.5 rounded font-mono text-[10px]">/verify</code>).
                                </p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="enable_ballot_verification" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Broadcast / Announcement Banner -->
                    <div class="pt-2">
                        <label for="announcement_banner" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pengumuman / Pesan Berjalan (Opsional)
                        </label>
                        <input 
                            type="text" 
                            id="announcement_banner" 
                            wire:model="announcement_banner"
                            placeholder="Contoh: Batas waktu pemilihan ditutup tepat pukul 16:00 WIB. Harap segera gunakan hak suara Anda!"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-500 text-xs text-slate-900 focus:outline-hidden focus:ring-3 focus:ring-blue-100 transition"
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Jika diisi, pesan ini dapat ditampilkan sebagai informasi broadcast penting bagi seluruh pemilih.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bottom Actions -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition cursor-pointer"
            >
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Pengaturan
                </span>
                <span wire:loading wire:target="save">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>
