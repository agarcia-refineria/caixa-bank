document.addEventListener('DOMContentLoaded', function () {
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
