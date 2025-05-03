import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

Alpine.start();


document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('sortable-accounts');
    Sortable.create(container, {
        animation: 150,
        onEnd: function (evt) {
            const ids = [...container.children].map(el => el.dataset.id);

            fetch('/profile/accounts/order', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids })
            });
        }
    });
});
