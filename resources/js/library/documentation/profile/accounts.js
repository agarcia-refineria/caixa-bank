window.loadProfileAccountsTour = loadProfileAccountsTour;

function loadProfileAccountsTour(tour) {
    let routeName = window.routeName;

    if (routeName === "profile.accounts.edit" || routeName === "profile.import.edit" || routeName === "profile.export.edit") {
        addStep(tour,'profile-accounts-create-account');
        addStep(tour,'profile-accounts-import');
        addStep(tour,'profile-accounts-export');
        addStep(tour,'profile-accounts-forms');
    }

    if (routeName === "profile.transaction.edit" || routeName === "profile.balance.edit") {
        addStep(tour,'profile-accounts-transactions-table');
        addStep(tour,'profile-accounts-balances-table');
    }
}
