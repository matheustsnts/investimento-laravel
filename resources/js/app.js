// resources/js/app.js

import 'bootstrap';
import '../css/app.css';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal; // opcional, se quiser acessar via window.Swal

// Função para aplicar tema
function applyTheme(theme) {
    const body = document.body;
    const html = document.documentElement;
    const icon = document.getElementById('themeToggleIcon');

    if (!body || !html) return;

    if (theme === 'dark') {
        body.classList.remove('theme-light');
        body.classList.add('theme-dark');
        html.setAttribute('data-theme', 'dark');
        if (icon) {
            icon.classList.remove('bi-moon-stars');
            icon.classList.add('bi-sun');
        }
    } else {
        body.classList.remove('theme-dark');
        body.classList.add('theme-light');
        html.setAttribute('data-theme', 'light');
        if (icon) {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon-stars');
        }
    }
}

// Inicialização ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    const toggleBtn = document.getElementById('themeToggleBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // ... seu código existente (tema, etc.)

    // Delegação para formulários de delete com SweetAlert2
    document.querySelectorAll('.form-delete-confirm').forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const message = this.getAttribute('data-message') 
                || 'Tem certeza que deseja excluir este registro? Esta ação não pode ser desfeita.';

            Swal.fire({
                title: 'Tem certeza?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});