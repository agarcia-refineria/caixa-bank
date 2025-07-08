<?php

return [
    'default' => "No description available for this section.",

    'navigation-panel-control' => "The Control Panel page provides an overview of the user's financial status, allowing detailed tracking of income, expenses and balance evolution over a specific period.",
    'navigation-history' => "Shows interactive tables (DataTables) with detailed history of accounts, transactions and balances, allowing quick and clear filtering, sorting and searching of financial information.",
    'navigation-forecast' => "A simple calculator for quick calculations, useful for performing basic arithmetic operations without leaving the Control Panel.",
    'navigation-clock' => "Shows current time and a list of scheduled tasks, automating the import of transactions and balances without exposing sensitive information.",
    'navigation-configuration' => "Allows importing accounts, transactions and balances via API, as well as ordering API accounts to define their position in the Control Panel.",
    'navigation-month-selector' => "It is a filter available only in the Control Panel that allows selecting the month to view to update the financial data shown.",
    'navigation-lang-selector' => "Allows changing the interface language according to user preference.",
    'navigation-user-dropdown' => "Shows user information and provides access to manually create transactions and configure associated categories.",

    'index-total-amount' => "The total amount is the sum of all income and expenses for the selected month, providing a clear view of the current financial status.",
    'index-dashboard-sidebar' => "The Dashboard sidebar shows accounts for quick access to financial information for each one, facilitating navigation and control of personal finances.",
    'index-stat-current' => "The current balance of the selected account reflects the amount of money available at that time, allowing the user to know their current financial situation.",
    'index-stat-expenses' => "The total expenses for the selected month shows the sum of all expenses incurred, allowing the user to evaluate their financial behavior and adjust their spending habits.",
    'index-stat-income' => "The total income for the selected month shows the sum of all income received, allowing the user to evaluate their income generation capacity and plan their future finances.",
    'index-category-chart-all' => "The categories chart shows the distribution of expenses, allowing the user to identify spending patterns and areas for improvement in their financial management.",
    'index-category-chart-category' => "The category chart shows the distribution of expenses by categories, allowing the user to delve into their financial behavior and make informed decisions about their personal finances.",
    'index-balance-chart' => "The balance chart shows the evolution of the balance over time, allowing the user to visualize trends and changes in their financial situation, facilitating planning and control of their personal finances.",
    'index-transactions-table' => "The transactions table shows all transactions made in the filtered month, allowing the user to review and analyze their financial history in a clear and organized manner.",

    'history-accounts-table' => "The accounts table shows a summary of all user accounts, providing an overview of their financial situation and facilitating access to detailed information for each account.",
    'history-transactions-table' => "The transactions table shows a detailed history of all transactions made, allowing the user to filter, sort and search specific information about their financial movements.",
    'history-balances-table' => "The balances table shows a summary of the user's account balances, providing a clear view of their financial situation and facilitating tracking of their finances over time.",

    'forecast-dashboard-sidebar' => "The Dashboard sidebar shows accounts for quick access to financial information for each one, facilitating navigation and control of personal finances.",
    'forecast-paysheet-select' => "The paysheet select allows the user to choose a specific paysheet apply for the max income, facilitating access to detailed information about their income and expenses for that period.",
    'forecast-average-month-expenses-excluding-categories' => "The average monthly expenses excluding categories shows the average of expenses for the selected month, excluding specific categories, allowing the user to analyze their financial behavior without considering certain expenses.",
    'forecast-disable-transfers' => "Disabling transfers allows the user to exclude transfers between accounts from the forecast, focusing only on income and expenses, facilitating a clearer analysis of their financial situation.",
    'forecast-apply-expenses-monthly' => "Applying monthly expenses allows the user to apply the average monthly expenses to the current month, facilitating financial planning and control of their personal finances.",
    'forecast-chart-incomes-future' => "The future income chart shows the expected income for the next months, allowing the user to visualize their financial projections and plan their future finances effectively.",

    'clock-current-time' => "The current time shows the current time in the user's timezone, providing a reference for scheduled tasks and financial management.",
    'clock-schedule-times' => "Scheduled times are the times when tasks are executed to import transactions and balances, ensuring that the user's financial information is always up to date without requiring manual intervention.",

    'requests-update-accounts' => "Updating accounts from the API allows synchronizing your accounts and financial information with the most recent data, ensuring that the Control Panel reflects the current state of their finances.",
    'requests-update-all' => "Updating all balances and transactions from the API allows the user to keep their financial information up to date, ensuring that all accounts reflect the most recent and accurate data, facilitating effective financial management.",
    'requests-sortable-accounts' => "Sortable accounts allow the user to customize the layout of their accounts in the Control Panel, facilitating quick access to the most relevant financial information and adapting the interface to their personal preferences.",

    'profile-navigation-profile' => "User profile information, including name, email and personal configuration options.",
    'profile-navigation-bank' => "Bank configuration, where the user can manage their bank accounts, themes and task scheduling.",
    'profile-navigation-accounts' => "Account management, allowing the user to create, edit and delete accounts manually.",
    'profile-navigation-categories' => "Category management, where the user can create, edit and delete categories as well as add or change filters for transactions related to those categories.",

    'profile-information-form' => "Profile information form, where the user can update their name and email.",
    'profile-password-form' => "Password change form, allowing the user to update their current password to a new one.",
    'profile-delete-form' => "Profile deletion form, where the user can permanently delete their account, ensuring they understand the implications of this action.",

    'profile-accounts-create-account' => "Account creation form, allowing the user to create a new account with the necessary details.",
    'profile-accounts-import' => "Import accounts from CSV or XLSX files, allowing the user to import account data in bulk.",
    'profile-accounts-export' => "Export accounts to CSV or XLSX files, allowing the user to download their account data for backup or external use.",
    'profile-accounts-forms' => "Account management forms, allowing the user to create, edit and delete accounts, as well as manage their transactions and balances only non api accounts.",
    'profile-accounts-transactions-table' => "Transactions table for accounts, allowing the user to view, filter and manage transactions associated with their accounts.",
    'profile-accounts-balances-table' => "Balances table for accounts, allowing the user to view, filter and manage balances associated with their accounts.",

    'profile-categories-create' => "Create categories form, allowing the user to create new categories for their transactions.",
    'profile-categories-update-transactions' => "Updat" . "e transactions categories form, allowing the user to set or change the categories associated with their transactions.",
    'profile-categories-forms' => "Category management forms, allowing the user to create, edit and delete categories, as well as manage their filters.",

    'configuration-profile-secrets' => "API keys settings, allowing the user to manage their API keys for importing accounts, transactions and balances from their banking institutions.",
    'configuration-profile-institutions' => "Banking institutions settings, allowing the user to manage their banking institutions and add the necessary API keys to import accounts, transactions, and balances.",
    'configuration-profile-chars' => "Control Panel Charts settings, allowing the user to customize the display of charts and statistics.",
    'configuration-profile-theme' => "Theme settings, where the user can select the visual theme of the Control Panel, customizing the appearance according to their preferences.",
    'configuration-profile-accounts-update' => "Account update settings, allowing the user to update their bank account information from the API through scheduled tasks.",
    'configuration-profile-accounts-info' => "Bank account information, with the total of API and manual accounts, plus the number of synchronizations performed on the current day.",
    'configuration-profile-lang' => "Language settings, allowing the user to select the interface language according to their preferences.",
];
