<div class="max-w-xl mx-auto py-8">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl text-center space-y-6">
        <!-- Success Icon Badge -->
        <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-4xl mx-auto shadow-lg shadow-emerald-500/10 border border-emerald-100">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                <i class="fa-solid fa-shield-halved text-[10px]"></i> Suara Terkirim & Terenkripsi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Terima Kasih atas Partisipasi Anda!</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Hak suara Anda pada sesi pemilihan <strong>{{ $election->name }}</strong> telah berhasil dicatat dan diverifikasi oleh sistem.
            </p>
        </div>

        <!-- Anonymous Receipt Box -->
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-left space-y-3.5 text-xs">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Bukti Partisipasi Resmi</span>
                <span class="text-[10px] text-slate-400 font-mono">SEAL-VERIFIED</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-500">Sesi Pemilihan:</span>
                <strong class="text-slate-900 text-right truncate max-w-[240px]">{{ $election->name }}</strong>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-500">Waktu Pencoblosan:</span>
                <strong class="text-slate-900">{{ now()->format('d M Y, H:i:s') }} {{ \App\Enums\IndonesianTimezone::currentAbbr() }}</strong>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-slate-500">Status Surat Suara:</span>
                <span class="inline-flex items-center gap-1 text-emerald-700 font-bold bg-emerald-100/60 px-2 py-0.5 rounded">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> SAH & TERCATAT
                </span>
            </div>

            <div class="pt-2 border-t border-slate-200 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-[11px]">Kode Referensi / Token Anonim:</span>
                    <a href="{{ route('ballot.verify', ['token' => $refCode]) }}" target="_blank" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Cek Verifikasi
                    </a>
                </div>
                <code class="p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 font-mono text-[11px] break-all select-all font-bold">
                    {{ $refCode }}
                </code>
                <span class="text-[10px] text-slate-400">
                    *Simpan kode ini sebagai bukti sah bahwa suara Anda telah dihitung di kotak suara digital.
                </span>
            </div>
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
            <a href="{{ route('voter.dashboard') }}" class="w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition text-center">
                Kembali ke Portal
            </a>
            <a href="{{ route('ballot.verify', ['token' => $refCode]) }}" target="_blank" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition text-center flex items-center justify-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Verifikasi Tanda Terima</span>
            </a>
            @if ($election->result_visibility->value !== 'hidden')
                <a href="{{ route('voter.elections.results', $election) }}" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition text-center">
                    Hasil Suara
                </a>
            @endif
        </div>
    </div>
</div>
