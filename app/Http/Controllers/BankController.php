<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class BankController extends Controller
{
    /**
     * Display a list of the user's bank accounts.
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $accounts = auth()->user()->accounts()->sortOrder()->get();

        $currentAccount = $accounts->first();

        $balance = $currentAccount->balances()->balanceTypeForward()->lastInstance();

        return view('pages.banks.index', compact('accounts', 'currentAccount', 'balance'));
    }

    /**
     * Display the specified bank account details.
     *
     * @param int|string $id The ID of the account to display.
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application The view containing account and bank data.
     */
    public function show($id): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $accounts = auth()->user()->accounts()->sortOrder()->get();

        $currentAccount = Account::where('id', $id)->first();

        $balance = $currentAccount->balances()->balanceTypeForward()->lastInstance();


        return view('pages.banks.index', compact('accounts', 'currentAccount', 'balance'));
    }

    /**
     * Display the transaction history of the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function history(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $user = auth()->user();

        $transactions = $user->transactions;

        return view('pages.banks.history', compact('user', 'transactions'));
    }

    /**
     * Fetches the user's schedule and returns a view displaying the bank schedules.
     *
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application The rendered view with the user's schedule data.
     */
    public function clock(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $schedules = auth()->user()->schedule;

        return view('pages.banks.clock', compact('schedules'));
    }

    /**
     * Retrieves all banks from the database and returns a view displaying their configuration.
     *
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application The rendered view with the banks data.
     */
    public function configuration(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $user = auth()->user();

        $accounts = $user->accounts()->sortOrder()->get();

        $showUpdateAccounts = true;
        foreach ($accounts as $account) {
            if ($account->transactionsDisabled || $account->balanceDisabled || $account->bankDataSyncTransactionsCount > \App\Models\ScheduledTasks::$MAX_TIMES || $account->bankDataSyncBalancesCount > \App\Models\ScheduledTasks::$MAX_TIMES) {
                $showUpdateAccounts = false;
            }
        }

        return view('pages.banks.configuration', compact( 'user', 'accounts', 'showUpdateAccounts'));
    }
}
