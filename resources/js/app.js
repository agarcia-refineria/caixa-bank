import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('month');

    container.addEventListener('change', function () {
        fetch('/month/' + container.value, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        }).then(
            response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error('Failed to fetch data');
                }
            }
        ).catch(error => {
            console.error('Error:', error);
        });
    })
});
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

// Execute schedule tasks each minute to see if update is needed
document.addEventListener('DOMContentLoaded', function () {
    console.log('Checking schedule...');

    setInterval(() => {
        checkSchedule();
    }, 60000); // 1 minute

    function checkSchedule() {
        fetch('/profile/schedule/check', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        }).then(response => response.json()).then(data => {
            if (data.update) {
                window.location.reload();
            }
        });
    }
});
