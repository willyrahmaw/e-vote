<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ $user->isAdmin() ? route('admin.dashboard') : route('voter.dashboard') }}" class="text-xs font-medium text-slate-500 hover:text-blue-600 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard
                </a>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Profil & Keamanan Akun</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kredensial keamanan dan kata sandi akun Anda.</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($user->isUsingDefaultPassword())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200/60 text-xs font-semibold">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Password Masih Default</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-xs font-semibold">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Kata Sandi Aman</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Default Password Alert Notification (if active) -->
    @if ($user->isUsingDefaultPassword())
        <div class="p-5 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white shadow-lg shadow-red-500/15 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative overflow-hidden border border-red-400/30">
            <div class="flex items-start gap-3.5 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shrink-0 shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-white">Peringatan Keamanan: Kata Sandi Default</h3>
                    <p class="text-xs text-red-100 mt-0.5 leading-relaxed">
                        Akun Anda saat ini masih menggunakan kata sandi standar sistem (<code class="bg-black/25 px-1.5 py-0.5 rounded font-mono text-white">password</code>). Harap segera ubah kata sandi Anda di bawah ini demi privasi dan keamanan hak suara Anda.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Grid: User Info Card & Password Change Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Account Details (Read-only) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-6">
            <div>
                <!-- User Avatar & Headline -->
                <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 text-base leading-tight">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-500 truncate max-w-[200px] mt-0.5">{{ $user->email }}</p>
                        <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $user->isAdmin() ? 'Administrator' : 'Pemilih (Voter)' }}
                        </span>
                    </div>
                </div>

                <!-- Detail Meta List -->
                <div class="mt-5 space-y-3.5">
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">NIM / NIK / Identifier</label>
                        <p class="text-xs font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                            {{ $user->identifier ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Email Terdaftar</label>
                        <p class="text-xs font-medium text-slate-800 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100 truncate">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Akun</label>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <span class="text-xs font-medium text-slate-700">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Terakhir Login</label>
                        <p class="text-xs text-slate-600">
                            {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Pembaruan Password</label>
                        <p class="text-xs text-slate-600">
                            {{ $user->password_changed_at ? $user->password_changed_at->format('d M Y, H:i') : 'Belum pernah diganti' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                <i class="fa-solid fa-lock text-slate-400 mr-1"></i> Data profil utama dikelola secara terpusat oleh administrator untuk menjaga integritas DPT pemilihan.
            </div>
        </div>

        <!-- Right: Change Password Form -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                <div>
                    <h2 class="font-bold text-slate-900 text-base">Ganti Kata Sandi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui kata sandi Anda untuk memastikan keamanan akun.</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                    <i class="fa-solid fa-key"></i>
                </div>
            </div>

            @if (session()->has('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="updatePassword" class="space-y-5">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input 
                            type="password" 
                            id="current_password"
                            wire:model="current_password"
                            placeholder="Masukkan kata sandi saat ini"
                            class="w-full pl-9 pr-10 py-2.5 rounded-xl border {{ $errors->has('current_password') ? 'border-rose-400 focus:ring-rose-200' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }} text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-3 transition"
                        />
                        <button type="button" data-toggle-password="current_password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-rose-500 text-[11px] mt-1.5 flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key text-xs"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password"
                            wire:model="password"
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-9 pr-10 py-2.5 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-200' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }} text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-3 transition"
                        />
                        <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-500 text-[11px] mt-1.5 flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-check-double text-xs"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            placeholder="Ketik ulang kata sandi baru"
                            class="w-full pl-9 pr-10 py-2.5 rounded-xl border {{ $errors->has('password_confirmation') ? 'border-rose-400 focus:ring-rose-200' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }} text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:ring-3 transition"
                        />
                        <button type="button" data-toggle-password="password_confirmation" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-rose-500 text-[11px] mt-1.5 flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Requirements Box -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600 space-y-1">
                    <p class="font-semibold text-slate-700"><i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Ketentuan Kata Sandi yang Baik:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-500 pl-1">
                        <li>Minimal 8 karakter.</li>
                        <li>Berbeda dari kata sandi saat ini dan tidak mudah ditebak.</li>
                        <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol untuk keamanan maksimal.</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex justify-end">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition cursor-pointer flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="updatePassword">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kata Sandi Baru
                        </span>
                        <span wire:loading wire:target="updatePassword">
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
