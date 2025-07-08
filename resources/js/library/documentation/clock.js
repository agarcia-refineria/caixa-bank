window.loadClockTour = loadClockTour;

function loadClockTour(tour) {
    let routeName = window.routeName;

    if (routeName === "dashboard.clock") {
        addStep(tour,'clock-current-time');
        addStep(tour,'clock-schedule-times');
    }
}
