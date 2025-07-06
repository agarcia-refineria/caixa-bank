<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ScheduledTasks;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    /**
     * Retrieves the authenticated user's accounts, including their balance details,
     * and returns a view displaying the dashboard with the accounts and balance information.
     *
     * @return View The rendered view of the dashboard with account data and balance details.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 401).
     */
    public function index(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        foreach ($accounts as $account) {
            $account->load(['balances' => function ($query) use ($account) {
                $query->balanceTypeForward($account)->lastInstance();
            }]);
        }

        $currentAccount = $accounts->first();
        $balance = $currentAccount?->balances->first();

        return view('pages.dashboard.index', compact('user', 'accounts', 'currentAccount', 'balance'));
    }

    /**
     * Displays the dashboard view with user accounts and the current account's balance data.
     *
     * @param string $id The ID of the current account to display.
     *
     * @throws HttpException If the authenticated user is not found.
     * @throws ModelNotFoundException If the specified account ID is not found.
     */
    public function show(string $id): View|\Illuminate\Foundation\Application|Factory|Application|RedirectResponse
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        $currentAccount = $user->accounts()
            ->findOrFail($id);

        $currentAccount->load(['balances' => function ($query) use ($currentAccount) {
            $query->balanceTypeForward($currentAccount)->lastInstance();
        }]);

        if ($currentAccount instanceof Account) {
            $balance = $currentAccount->balances->first();

            return view('pages.dashboard.index', compact('user', 'accounts', 'currentAccount', 'balance'));
        }

        return redirect()->route('dashboard.index')->with(['status' => __('status.dashboardcontroller.account-not-found')]);
    }

    /**
     * Retrieves the authenticated user's transaction history, accounts, and returns a view displaying the data.
     *
     * @return View The rendered view with the user's transactions and accounts information.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function history(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $transactions = $user->transactions;
        $balances = $user->balances;
        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        return view('pages.dashboard.history', compact('user', 'transactions', 'accounts', 'balances'));
    }

    /**
     * Displays a view of the user's accounts in a calculator dashboard.
     *
     * @return View The rendered view of the calculator page with user's accounts data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function forecast(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        $currentAccount = $accounts->first();

        return view('pages.dashboard.forecast', compact('user', 'accounts', 'currentAccount'));
    }

    /**
     * Displays the forecast information for a specific account and returns the corresponding view.
     *
     * @param string $id The identifier of the account to retrieve forecast data for.
     *
     * @return View The rendered view of the forecast page with the user's accounts and the current account data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     * @throws ModelNotFoundException Thrown when the specified account cannot be located for the given ID.
     */
    public function forecastShow(string $id): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        $currentAccount = $user->accounts()
            ->findOrFail($id);

        $currentAccount->load(['balances' => function ($query) use ($currentAccount) {
            $query->balanceTypeForward($currentAccount)->lastInstance();
        }]);

        return view('pages.dashboard.forecast', compact('user', 'accounts', 'currentAccount'));
    }

    /**
     * Clocks the user's schedule and returns a view displaying the clock data.
     *
     * @return View The rendered view of the clock page with user's schedule data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function clock(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $schedules = $user->schedule;

        return view('pages.dashboard.clock', compact('user', 'schedules'));
    }


    /**
     * Displays the user's configuration page with account details and settings.
     *
     * @return View The rendered view of the configuration page with user and account data.
     *
     * @throws HttpException Thrown when the authenticated user cannot be found (HTTP 403).
     */
    public function requests(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $apiAccounts = tap($user->accounts()->getQuery())
            ->onlyApi()
            ->sortOrder()
            ->get();

        $showUpdateAccounts = $apiAccounts->isNotEmpty() && $apiAccounts->every(function ($account) {
            return !$account->transactionsDisabled
                && !$account->balanceDisabled
                && $account->bankDataSyncTransactionsCount <= ScheduledTasks::$MAX_TIMES
                && $account->bankDataSyncBalancesCount <= ScheduledTasks::$MAX_TIMES;
        });

        $accounts = $user->accounts()
            ->orderBy('order')
            ->get();

        return view('pages.dashboard.requests', compact('user', 'accounts', 'showUpdateAccounts'));
    }
}
