// Digital Clock & Realtime Vote Toast Notifications for Live Screen Display
document.addEventListener('DOMContentLoaded', () => {
    // 1. Digital Clock
    function updateLiveClock() {
        const clockEl = document.getElementById('live-digital-clock');
        const dateEl = document.getElementById('live-digital-date');
        if (!clockEl && !dateEl) return;

        const now = new Date();
        const tzAbbr = clockEl ? (clockEl.getAttribute('data-timezone-abbr') || 'WIB') : 'WIB';

        if (clockEl) {
            clockEl.textContent = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }) + ' ' + tzAbbr;
        }
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    // 2. Realtime Vote Toast Notification System
    function showVoteToast(data) {
        const container = document.getElementById('live-vote-toast-container');
        if (!container) return;

        const uuid = (data && data.uuid) ? data.uuid : 'Token Terenkripsi';
        const time = (data && data.time) ? data.time : new Date().toLocaleTimeString('id-ID');

        const toast = document.createElement('div');
        toast.className = 'bg-white border-l-4 border-emerald-500 border-t border-r border-b border-slate-200 rounded-xl p-3.5 shadow-xl flex items-center gap-3.5 min-w-[300px] max-w-sm pointer-events-auto transform transition-all duration-300 translate-y-3 opacity-0';
        
        toast.innerHTML = `
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0 border border-emerald-100">
                <i class="fa-solid fa-check-to-slot"></i>
            </div>
            <div class="overflow-hidden flex-1">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-bold text-slate-900 leading-tight">Suara Baru Masuk</p>
                    <span class="text-[10px] text-slate-400 font-mono">${time}</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] text-slate-500 font-medium">Anonim:</span>
                    <span class="font-mono font-bold text-[11px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 tracking-wider">${uuid}</span>
                </div>
            </div>
        `;

        container.appendChild(toast);

        // Animate In
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-3', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto Animate Out & Remove after 4.5 seconds
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-3', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4500);
    }

    // Listen to Livewire event
    if (window.Livewire) {
        window.Livewire.on('new-vote-received', (eventData) => {
            const payload = Array.isArray(eventData) ? eventData[0] : eventData;
            showVoteToast(payload);
        });
    }

    window.addEventListener('new-vote-received', (e) => {
        showVoteToast(e.detail);
    });
});
