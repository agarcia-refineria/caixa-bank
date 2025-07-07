window.loadProfileConfigurationTour = loadProfileConfigurationTour;

function loadProfileConfigurationTour() {
    let routeName = window.routeName;

    if (routeName === "profile.configuration.edit") {
        addStep('configuration-profile-bank');
        addStep('configuration-profile-lang');
        addStep('configuration-profile-chars');
        addStep('configuration-profile-theme');
        addStep('configuration-profile-accounts-update');
        addStep('configuration-profile-accounts-info');
    }
}
