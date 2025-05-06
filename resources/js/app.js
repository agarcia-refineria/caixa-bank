import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import select2 from 'select2';
select2();

import 'datatables.net-dt';
import moment from 'moment';

import "/node_modules/select2/dist/css/select2.css";

import { Chart, ArcElement, Legend, CategoryScale, LineController, LineElement, PointElement, LinearScale, Title, DoughnutController } from 'chart.js';

Chart.register(ArcElement, Legend, CategoryScale, LineController, LineElement, PointElement, LinearScale, Title, DoughnutController);

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function() {
    console.log('jQuery is working!');
});

// SELECT2 JS
$(document).ready(function() {
    // Initialize Select2
    console.log('Select2 is working!');
    $('.select2').select2({
        placeholder: '-- Select an option --',
        allowClear: true
    });
});

// DATATABLES JS
$(document).ready(function () {
    console.log('DataTable is working!');
    $('.datatable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            { targets: 1, type: 'date' },
            { orderable: false, targets: 'no-sort' }
        ],
        order: [[1, 'desc']],
        layout: {
            bottomEnd: {
                paging: {
                    firstLast: false
                }
            }
        }
    });
});

// FETCH REQUESTS JS
$(document).ready(function () {
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
$(document).ready(function () {
    const container = document.getElementById('sortable-accounts');
    if (container) {
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
    }
});

// Execute schedule tasks each minute to see if update is needed
$(document).ready(function () {
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

// CHARTS JS
$(document).ready(function () {
    const categoryCtx = document.getElementById('categoryChart');
    setCategoryChart();

    function setCategoryChart() {
        var labels = categoryCtx.getAttribute('data-labels').split(",");
        var values = categoryCtx.getAttribute('data-values').split(",").map(Number);
        var colors = categoryCtx.getAttribute('data-colors').split(",");

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Expenses',
                    data: values,
                    backgroundColor: colors,
                }]
            }
        });
    }

    const balanceCtx = document.getElementById('balanceChart');
    setBalanceChart();

    function setBalanceChart() {
        var labels = balanceCtx.getAttribute('data-labels').split(",");
        var values = balanceCtx.getAttribute('data-values').split(",").map(Number);

        new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Balance',
                    data: values,
                    borderColor: '#108cb9',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }]
            }
        });
    }
})

