window.loadForecastTour = loadForecastTour;

function loadForecastTour(tour) {
    let routeName = window.routeName;

    if (routeName === "dashboard.forecast" || routeName === "dashboard.forecastShow") {
        addStep(tour,'forecast-dashboard-sidebar');
        addStep(tour,'forecast-paysheet-select');
        addStep(tour,'forecast-average-month-expenses-excluding-categories');
        addStep(tour,'forecast-disable-transfers');
        addStep(tour,'forecast-apply-expenses-monthly');
        addStep(tour,'forecast-chart-incomes-future');
    }
}
