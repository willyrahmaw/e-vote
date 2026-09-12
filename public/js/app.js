// Global App JS
document.addEventListener('DOMContentLoaded', () => {
    // Universal Password Visibility Toggle Handler
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('[data-toggle-password]');
        if (!toggleBtn) return;

        const targetInputId = toggleBtn.getAttribute('data-toggle-password');
        const input = document.getElementById(targetInputId);
        if (!input) return;

        const icon = toggleBtn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });
});

