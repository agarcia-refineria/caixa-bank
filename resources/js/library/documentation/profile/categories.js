window.loadProfileCategoriesTour = loadProfileCategoriesTour;

function loadProfileCategoriesTour(tour) {
    let routeName = window.routeName;

    if (routeName === "profile.categories") {
        addStep(tour,'profile-categories-create');
        addStep(tour,'profile-categories-update-transactions');
        addStep(tour,'profile-categories-forms');
    }
}
