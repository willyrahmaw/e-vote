// SweetAlert2 Event Handlers for Livewire
document.addEventListener('DOMContentLoaded', () => {
    // Success Toast / Alert
    window.addEventListener('swal:success', (event) => {
        const detail = event.detail?.[0] || event.detail || {};
        Swal.fire({
            icon: 'success',
            title: detail.title || 'Berhasil!',
            text: detail.message || (typeof detail === 'string' ? detail : 'Operasi berhasil dilakukan.'),
            confirmButtonColor: '#3b82f6',
            timer: detail.timer || 3000,
            timerProgressBar: true,
        });
    });

    // Error Alert
    window.addEventListener('swal:error', (event) => {
        const detail = event.detail?.[0] || event.detail || {};
        Swal.fire({
            icon: 'error',
            title: detail.title || 'Gagal!',
            text: detail.message || (typeof detail === 'string' ? detail : 'Terjadi kesalahan.'),
            confirmButtonColor: '#ef4444',
        });
    });

    // Warning / Notice Alert
    window.addEventListener('swal:warning', (event) => {
        const detail = event.detail?.[0] || event.detail || {};
        Swal.fire({
            icon: 'warning',
            title: detail.title || 'Perhatian!',
            text: detail.message || (typeof detail === 'string' ? detail : 'Peringatan.'),
            confirmButtonColor: '#f59e0b',
        });
    });

    // Confirmation Alert (Dispatches back to Livewire component)
    window.addEventListener('swal:confirm', (event) => {
        const detail = event.detail?.[0] || event.detail || {};
        Swal.fire({
            title: detail.title || 'Apakah Anda yakin?',
            text: detail.text || 'Tindakan ini tidak dapat dibatalkan.',
            icon: detail.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: detail.confirmButtonColor || '#3b82f6',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: detail.confirmText || 'Ya, Lanjutkan',
            cancelButtonText: detail.cancelText || 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                if (detail.method && window.Livewire) {
                    const component = Livewire.find(detail.componentId);
                    if (component) {
                        component.call(detail.method, detail.params || null);
                    }
                }
            }
        });
    });

    // Global Logout Confirmation Dialog
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[action*="logout"]') || (e.target.action && e.target.action.includes('logout') ? e.target : null);
        if (!form) return;
        if (form.dataset.confirmed === 'true') return;

        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari sistem e-voting?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1"></i> Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });
});

