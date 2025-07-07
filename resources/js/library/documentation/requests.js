window.loadRequestsTour = loadRequestsTour;

function loadRequestsTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.requests") {
        addStep('requests-update-accounts');
        addStep('requests-update-all');
        addStep('requests-sortable-accounts');
    }
}
