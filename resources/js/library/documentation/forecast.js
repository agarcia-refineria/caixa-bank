window.loadForecastTour = loadForecastTour;

function loadForecastTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.forecast" || routeName === "dashboard.forecastShow") {
        addStep('forecast-dashboard-sidebar');
        addStep('forecast-paysheet-select');
        addStep('forecast-average-month-expenses-excluding-categories');
        addStep('forecast-disable-transfers');
        addStep('forecast-apply-expenses-monthly');
        addStep('forecast-chart-incomes-future');
    }
}
