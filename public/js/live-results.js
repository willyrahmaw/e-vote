// Live Results Dynamic Progress Bars & Presentation Monitor Features
document.addEventListener('DOMContentLoaded', () => {
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('[data-progress-width]');
        progressBars.forEach((bar) => {
            const targetWidth = bar.getAttribute('data-progress-width');
            if (targetWidth !== null) {
                bar.style.width = targetWidth + '%';
            }
        });
    }

    function initFullscreenToggle() {
        const toggleButtons = document.querySelectorAll('[data-toggle-fullscreen]');
        toggleButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch((err) => {
                        console.error(`Gagal mengaktifkan mode layar penuh: ${err.message}`);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });
        });
    }

    animateProgressBars();
    initFullscreenToggle();

    document.addEventListener('livewire:navigated', () => {
        animateProgressBars();
        initFullscreenToggle();
    });

    window.addEventListener('livewire:rendered', animateProgressBars);
});
