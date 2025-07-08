window.loadRequestsTour = loadRequestsTour;

function loadRequestsTour(tour) {
    let routeName = window.routeName;

    if (routeName === "dashboard.requests") {
        addStep(tour,'requests-update-accounts');
        addStep(tour,'requests-update-all');
        addStep(tour,'requests-sortable-accounts');
    }
}
