window.loadProfileCategoriesTour = loadProfileCategoriesTour;

function loadProfileCategoriesTour() {
    let routeName = window.routeName;

    if (routeName === "profile.categories") {
        addStep('profile-categories-create');
        addStep('profile-categories-update-transactions');
        addStep('profile-categories-forms');
    }
}
