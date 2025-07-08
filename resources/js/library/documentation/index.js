window.loadIndexTour = loadIndexTour;

function loadIndexTour(tour) {
    let routeName = window.routeName;

    if (routeName === "dashboard.index" || routeName === "dashboard.show") {
        addStep(tour,'index-total-amount');
        addStep(tour,'index-dashboard-sidebar', 'right');

        addStep(tour,'index-stat-current');
        addStep(tour,'index-stat-expenses');
        addStep(tour,'index-stat-income');

        addStep(tour,'categoryChart');
        addStep(tour,'balanceChart');

        addStep(tour,'index-transactions-table');
    }
}
