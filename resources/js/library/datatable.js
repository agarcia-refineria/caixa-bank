import $ from "jquery";

$(document).ready(function () {
    console.log('DataTable is working!');
    $('.datatable').DataTable({
        pagingType: 'numbers',
        responsive: {
            details: {
                type: 'inline',
                target: 'tr'
            }
        },
        pageLength: 10,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/' + window.currentLocale + '.json'
        },
        columnDefs: [
            { targets: 'dt-low-priority', responsivePriority: 10001 },
            { targets: 'dt-high-priority', responsivePriority: 1 },
            { targets: 'dt-medium-priority', responsivePriority: 2 },

            { targets: 'dt-date', type: 'date' },
            { orderable: false, targets: 'no-sort' }
        ],
        order: [[1, 'desc']],
        drawCallback: function (settings) {
            let api = this.api();
            let rows = api.rows({ search: 'applied' }).count();

            let tableContainer = $(api.table().container());

            let paginationNav = tableContainer.find('nav[aria-label="pagination"]');

            if (rows <= settings._iDisplayLength) {
                paginationNav.hide();
            } else {
                paginationNav.show();
            }
        },
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let total = 0;

            // Sumar todos los data-amount por página
            //pi.rows({ page: 'current' }).nodes().each(function (row) {
            //   let amountStr = row.getAttribute('data-amount') || '0';
            //   let parsed = parseFloat(amountStr.replace(/\./g, '').replace(',', '.')) || 0;
            //   total += parsed;
            //);

            // Sumar todos los data-amount por todas las páginas
            //api.rows().nodes().each(function (row) {
            //    let amountStr = row.getAttribute('data-amount') || '0';
            //    let parsed = parseFloat(amountStr.replace(/\./g, '').replace(',', '.')) || 0;
            //    total += parsed;
            //});

            // Sumar todos los data-amount por todas las paginas con filtro aplicado
            api.rows({ search: 'applied' }).nodes().each(function (row) {
                let amountStr = row.getAttribute('data-amount') || '0';
                let parsed = parseFloat(amountStr.replace(/\./g, '').replace(',', '.')) || 0;
                total += parsed;
            });

            // Mostrar el total en el pie de tabla
            $(row.querySelector('td:last-child')).html(total.toLocaleString(window.currentLocale, {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 2
            }));
        }
    });
});
