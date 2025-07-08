window.loadProfileConfigurationTour = loadProfileConfigurationTour;

function loadProfileConfigurationTour(tour) {
    let routeName = window.routeName;

    if (routeName === "profile.configuration.edit") {
        addStep(tour,'configuration-profile-secrets');
        addStep(tour, 'configuration-profile-institutions');
        addStep(tour,'configuration-profile-lang');
        addStep(tour,'configuration-profile-chars');
        addStep(tour,'configuration-profile-theme');
        addStep(tour,'configuration-profile-accounts-update');
        addStep(tour,'configuration-profile-accounts-info');
    }
}
