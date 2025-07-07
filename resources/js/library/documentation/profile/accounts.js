window.loadProfileAccountsTour = loadProfileAccountsTour;

function loadProfileAccountsTour() {
    let routeName = window.routeName;

    if (routeName === "profile.accounts.edit" || routeName === "profile.import.edit" || routeName === "profile.export.edit") {
        addStep('profile-accounts-create-account');
        addStep('profile-accounts-import');
        addStep('profile-accounts-export');
        addStep('profile-accounts-forms');
    }

    if (routeName === "profile.transaction.edit" || routeName === "profile.balance.edit") {
        addStep('profile-accounts-transactions-table');
        addStep('profile-accounts-balances-table');
    }
}
