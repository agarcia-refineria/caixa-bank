window.loadClockTour = loadClockTour;

function loadClockTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.clock") {
        addStep('clock-current-time');
        addStep('clock-schedule-times');
    }
}
