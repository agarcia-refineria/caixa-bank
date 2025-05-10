import $ from "jquery";
import {Chart} from "chart.js";

$(document).ready(function () {
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        setCategoryChart();
    }

    function setCategoryChart() {
        var labels = categoryCtx.getAttribute('data-labels').split(",");
        var values = categoryCtx.getAttribute('data-values').split(",").map(Number);
        var colors = categoryCtx.getAttribute('data-colors').split(",");

        console.log('Chart is working!');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Expenses',
                    data: values,
                    backgroundColor: colors,
                }]
            },
        });
    }

    const balanceCtx = document.getElementById('balanceChart');
    if (balanceCtx) {
        setBalanceChart();
    }

    function setBalanceChart() {
        var labels = balanceCtx.getAttribute('data-labels').split(",");
        var values = balanceCtx.getAttribute('data-values').split(",").map(Number);

        console.log('Chart is working!');
        new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Balance',
                    data: values,
                    borderColor: '#2d43b0',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }]
            }
        });
    }
})
