// Voting Countdown and Interactions
document.addEventListener('DOMContentLoaded', () => {
    function initCountdowns() {
        const countdownElements = document.querySelectorAll('[data-countdown]');
        
        countdownElements.forEach((el) => {
            const startStr = el.getAttribute('data-start');
            const endStr = el.getAttribute('data-end');
            
            if (!startStr || !endStr) return;
            
            const startTime = new Date(startStr).getTime();
            const endTime = new Date(endStr).getTime();
            
            const daysEl = el.querySelector('[data-days]');
            const hoursEl = el.querySelector('[data-hours]');
            const minutesEl = el.querySelector('[data-minutes]');
            const secondsEl = el.querySelector('[data-seconds]');
            const labelEl = el.querySelector('[data-countdown-label]');
            
            function updateTimer() {
                const now = new Date().getTime();
                let targetTime = endTime;
                let isUpcoming = false;
                
                if (now < startTime) {
                    targetTime = startTime;
                    isUpcoming = true;
                    if (labelEl) labelEl.textContent = 'Pemilihan Dimulai Dalam:';
                } else if (now >= startTime && now <= endTime) {
                    targetTime = endTime;
                    if (labelEl) labelEl.textContent = 'Pemilihan Berakhir Dalam:';
                } else {
                    if (labelEl) labelEl.textContent = 'Pemilihan Telah Berakhir';
                    if (daysEl) daysEl.textContent = '00';
                    if (hoursEl) hoursEl.textContent = '00';
                    if (minutesEl) minutesEl.textContent = '00';
                    if (secondsEl) secondsEl.textContent = '00';
                    return;
                }
                
                const diff = targetTime - now;
                if (diff <= 0) {
                    if (labelEl) labelEl.textContent = isUpcoming ? 'Sesi Pemilihan Dibuka' : 'Pemilihan Selesai';
                    return;
                }
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
            }
            
            updateTimer();
            setInterval(updateTimer, 1000);
        });
    }

    initCountdowns();

    // Re-initialize when Livewire navigates or updates
    document.addEventListener('livewire:navigated', initCountdowns);
});
