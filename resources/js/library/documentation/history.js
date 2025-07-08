window.loadHistoryTour = loadHistoryTour;

function loadHistoryTour(tour) {
    let routeName = window.routeName;

    if (routeName === "dashboard.history") {
        addStep(tour,'history-accounts-table');
        addStep(tour,'history-balances-table');
        addStep(tour,'history-transactions-table');
    }
}
