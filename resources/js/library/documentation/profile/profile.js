window.loadProfileTour = loadProfileTour;

function loadProfileTour(tour) {
    let routeName = window.routeName;

    if (routeName === "profile.edit") {
        addStep(tour,'profile-information-form');
        addStep(tour,'profile-password-form');
        addStep(tour,'profile-delete-form');
    }
}
