<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NordigenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\BalancesController;
use App\Http\Controllers\Api\DatatableController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // This is the default route for the application
    return redirect()->route('dashboard.index');
});

Route::get('/lang/{locale}',[LangController::class, 'index'])->name('lang.switch');
Route::get('/month/{month}',[MonthController::class, 'index'])->name('month.index');

Route::middleware(['auth', 'web'])->group(function () {
    // Profile [Profile routes]
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile [Bank routes]
    Route::get('/profile/configuration', [ConfigurationController::class, 'edit'])->name('profile.configuration.edit');
    Route::post('/profile/apikey/view', [ConfigurationController::class, 'viewApi'])->name('profile.configuration.viewApi');
    Route::patch('/profile/configuration', [ConfigurationController::class, 'update'])->name('profile.configuration.update');
    Route::patch('/profile/configuration/institutions', [ConfigurationController::class, 'institutions'])->name('profile.configuration.institutions');
    Route::patch('/profile/configuration/chars', [ConfigurationController::class, 'chars'])->name('profile.configuration.chars');
    Route::patch('/profile/configuration/theme', [ConfigurationController::class, 'theme'])->name('profile.configuration.theme');
    Route::patch('/profile/configuration/lang', [ConfigurationController::class, 'lang'])->name('profile.configuration.lang');
    Route::patch('/profile/schedule', [ConfigurationController::class, 'schedule'])->name('profile.accounts.schedule');
    Route::post('/profile/schedule/check', [ConfigurationController::class, 'scheduleTasks'])->name('profile.accounts.scheduleTasks');

    // Profile [Account routes]
    Route::get('/profile/accounts', [AccountsController::class, 'edit'])->name('profile.accounts.edit');
    Route::post('/profile/accounts', [AccountsController::class, 'create'])->name('profile.account.create');
    Route::patch('/profile/accounts', [AccountsController::class, 'update'])->name('profile.account.update');
    Route::delete('/profile/accounts/{id}', [AccountsController::class, 'destroy'])->name('profile.account.destroy');
    Route::post('/profile/accounts/order', [AccountsController::class, 'reorder'])->name('profile.accounts.reorder');
    Route::post('/profile/accounts/paysheet', [AccountsController::class, 'paysheet'])->name('profile.account.paysheet');
    Route::post('/profile/accounts/disable-transactions', [AccountsController::class, 'disableTransactions'])->name('profile.account.disable-transactions');
    Route::post('/profile/accounts/apply-expenses-monthly', [AccountsController::class, 'applyExpensesMonthly'])->name('profile.account.apply-expenses-monthly');

    // Profile [Transaction routes]
    Route::get('/profile/transactions/{id}', [TransactionsController::class, 'edit'])->name('profile.transaction.edit');
    Route::post('/profile/transaction', [TransactionsController::class, 'create'])->name('profile.transaction.create');
    Route::patch('/profile/transaction', [TransactionsController::class, 'update'])->name('profile.transaction.update');
    Route::delete('/profile/transaction', [TransactionsController::class, 'destroy'])->name('profile.transaction.destroy');

    // Profile [Balance routes]
    Route::get('/profile/balances/{id}', [BalancesController::class, 'edit'])->name('profile.balance.edit');
    Route::post('/profile/balance', [BalancesController::class, 'create'])->name('profile.balance.create');
    Route::patch('/profile/balance', [BalancesController::class, 'update'])->name('profile.balance.update');
    Route::delete('/profile/balance', [BalancesController::class, 'destroy'])->name('profile.balance.destroy');

    // Profile [import routes]
    Route::get('/profile/import', [ImportController::class, 'show'])->name('profile.import.edit');
    Route::post('/profile/import/accounts', [ImportController::class, 'accounts'])->name('profile.import.accounts');
    Route::post('/profile/import/transactions', [ImportController::class, 'transaction'])->name('profile.import.transactions');
    Route::post('/profile/import/balances', [ImportController::class, 'balances'])->name('profile.import.balances');

    // Profile [export routes]
    Route::get('/profile/export', [ExportController::class, 'show'])->name('profile.export.edit');
    Route::post('/profile/export/accounts/{type}', [ExportController::class, 'accounts'])->name('profile.export.accounts');
    Route::post('/profile/export/transactions/{type}', [ExportController::class, 'transaction'])->name('profile.export.transactions');
    Route::post('/profile/export/balances/{type}', [ExportController::class, 'balances'])->name('profile.export.balances');

    // Profile [Categories routes]
    Route::get('/profile/categories', [CategoriesController::class, 'show'])->name('profile.categories');
    Route::post('/profile/categories', [CategoriesController::class, 'create'])->name('profile.category.create');
    Route::patch('/profile/categories/{id}', [CategoriesController::class, 'update'])->name('profile.category.update');
    Route::delete('/profile/categories/{id}', [CategoriesController::class, 'destroy'])->name('profile.category.destroy');
    Route::post('/profile/transactions-categories/update', [CategoriesController::class, 'setAllCategoriesFilter'])->name('profile.categories.update-transactions');

    // Profile [Categories filter routes]
    Route::post('/profile/categories/filter', [CategoriesController::class, 'createFilter'])->name('profile.categories.filter');
    Route::patch('/profile/categories/filter/{id}', [CategoriesController::class, 'updateFilter'])->name('profile.categories.filter.update');
    Route::delete('/profile/categories/filter/{id}', [CategoriesController::class, 'destroyFilter'])->name('profile.category.filter.destroy');

    // Profile [Datatable routes]
    Route::get('/datatable/accounts', [DatatableController::class, 'accounts'])->name('api.datatable.accounts');
    Route::get('/datatable/balances/{id?}', [DatatableController::class, 'balances'])->name('api.datatable.balances');
    Route::get('/datatable/transactions/{id?}', [DatatableController::class, 'transactions'])->name('api.datatable.transactions');

    // Panel [Panel routes]
    Route::get('/accounts', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/accounts/{id}', [DashboardController::class, 'show'])->name('dashboard.show');

    // Panel [History routes]
    Route::get('/history', [DashboardController::class, 'history'])->name('dashboard.history');

    // Panel [Forecast routes]
    Route::get('/forecast', [DashboardController::class, 'forecast'])->name('dashboard.forecast');
    Route::get('/forecast/{id}', [DashboardController::class, 'forecastShow'])->name('dashboard.forecastShow');

    // Panel [Clock routes]
    Route::get('/clock', [DashboardController::class, 'clock'])->name('dashboard.clock');

    // Panel [Configuration routes]
    Route::get('/requests', [DashboardController::class, 'requests'])->name('dashboard.requests');

    // Nordigen API routes
    Route::get('/nordigen/connect/{institutionId}', [NordigenController::class, 'authenticate'])->name('nordigen.auth');
    Route::get('/nordigen/callback', [NordigenController::class, 'callback'])->name('nordigen.callback');

    Route::get('/nordigen/transactions/{accountId}', [NordigenController::class, 'transactions'])->name('nordigen.transactions');
    Route::get('/nordigen/balances/{accountId}', [NordigenController::class, 'balances'])->name('nordigen.balances');

    Route::get('/nordigen/update/{accountId}', [NordigenController::class, 'update'])->name('nordigen.all');
    Route::get('/nordigen/update', [NordigenController::class, 'updateAll'])->name('nordigen.all_accounts');

    Route::get('/nordigen/institutions', [NordigenController::class, 'insertInstitutions'])->name('nordigen.institutions');
});


require __DIR__.'/auth.php';
