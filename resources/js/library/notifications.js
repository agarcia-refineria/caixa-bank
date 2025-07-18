document.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.alert:not(.alert-nordigen)');
    if (alert) {
        alert.classList.add('show');

        setTimeout(() => {
            alert.classList.remove('show');
        }, 3000); // Ocultar después de 3 segundos
    }

    const nordigen = document.querySelector('.alert.alert-nordigen');
    if (nordigen) {
        nordigen.classList.add('show');

        setTimeout(() => {
            nordigen.classList.remove('show');
        }, 5000); // Ocultar después de 10 segundos
    }
});
