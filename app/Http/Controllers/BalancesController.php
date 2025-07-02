<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class BalancesController extends Controller
{
    /**
     * Display the form to edit a specific account's balances.
     *
     * This method fetches the account information and relevant balances for the authenticated user.
     * If the account is not found or any error occurs, an error is logged, and a 505 error response is returned.
     *
     * @param string $accountId The unique identifier of the account to be edited.
     * @return View|RedirectResponse Returns the view for editing balances or aborts with an error response.
     *
     * @throws Exception If an error occurs or the account is not found, logs the error and aborts.
     */
    public function edit(string $accountId): View|RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        try {
            $account = Account::where('user_id', $user->id)
                ->findOrFail($accountId);

            return view('pages.profile.balances.edit', [
                'user' => $user,
                'account' => $account,
                'balances' => $account->balances()->orderBy('reference_date', 'desc')->get()
            ]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BalancesController')->error(
                'Error function edit()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            abort(505);
        }
    }

    /**
     * Create a new balance record for a specific account.
     *
     * This method validates the incoming request data, begins a database transaction to create a balance record linked
     * to a specific account, and commits the transaction if successful. If an error occurs, the transaction is rolled back,
     * and the user is redirected back with an error message.
     *
     * @param Request $request The incoming HTTP request containing balance creation data.
     * @return RedirectResponse Redirects to the edit balance view on success or back to the form with errors on failure.
     *
     * @throws Exception|Throwable If an error occurs during balance creation, the transaction is rolled back, and the error is logged.
     */
    public function create(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
        ]);

        $validated = array_merge($validated, $request->validate([
            'newBalance.amount' => 'required|numeric|min:0|decimal:0,2',
            'newBalance.currency' => 'required|string|size:3',
            'newBalance.balance_type' => 'required|string|in:' . implode(',', Balance::$balanceTypes),
            'newBalance.reference_date' => 'required|date_format:Y-m-d',
        ]));

        $balanceData = $validated['newBalance'];

        try {
            DB::beginTransaction();

            $account = Account::onlyManual()
                ->where('user_id', $user->id)
                ->findOrFail($validated['account_id']);

            Balance::create([
                'account_id' => $account->code,
                'amount' => $balanceData['amount'],
                'currency' => $balanceData['currency'],
                'balance_type' => $balanceData['balance_type'],
                'reference_date' => Carbon::createFromFormat('Y-m-d', $balanceData['reference_date']),
            ]);

            DB::commit();

            return Redirect::route('profile.balance.edit', ['id' => $account->code])
                ->with('success', __('status.balancescontroller.create-balance-success'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BalancesController')->error(
                'Error function create()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return Redirect::back()
                ->withInput()
                ->with('error', __('status.balancescontroller.create-balance-failed'));
        }
    }

    /**
     * Update the details of a specific balance.
     *
     * This method validates the request data, updates the balance record in the database,
     * and redirects the user to the appropriate view upon success or failure. If the
     * specified balance is not found or an error occurs, appropriate actions are taken
     * to log the error and notify the user.
     *
     * @param Request $request The HTTP request containing balance update details.
     * @return RedirectResponse Redirects to the edit view on success or redirects back
     *                          with error messages on failure.
     *
     * @throws ModelNotFoundException If the specified balance is not found.
     * @throws Exception|Throwable If any other error occurs during the update process, it is logged
     *                   and handled appropriately.
     */
    public function update(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validate([
            'balance_id' => 'required|exists:balances,id',
        ]);

        $key = $request->input('balance_id');

        $request->validate([
            "Balance.$key.amount" => 'required|numeric',
            "Balance.$key.currency" => 'required|string|size:3',
            "Balance.$key.balance_type" => 'required|string|in:' . implode(',', Balance::$balanceTypes),
            "Balance.$key.reference_date" => 'required|date_format:Y-m-d',
        ]);

        try {
            $balance = Balance::findOrFail($key);

            // Ensure the balance belongs to the authenticated user's account and is manual
            if ($balance->account->is_manual && $balance->account->user_id !== $user->id) {
                return Redirect::route('profile.accounts.edit')
                    ->with('error', __('status.balancescontroller.update-balance-not-found'));
            }

            $balanceData = $request->input('Balance')[$key];

            DB::transaction(function () use ($request, $balance, $balanceData) {
                $balance->update([
                    'amount' => $balanceData['amount'],
                    'currency' => strtoupper($balanceData['currency']),
                    'balance_type' => $balanceData['balance_type'],
                    'reference_date' => Carbon::createFromFormat('Y-m-d', $balanceData['reference_date']),
                ]);
            });

            return Redirect::route('profile.balance.edit', ['id' => $balance->account->code])
                ->with('success', __('status.balancescontroller.update-balance-success'));
        } catch (ModelNotFoundException $e) {
            $user->getCustomLoggerAttribute('BalancesController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.balancescontroller.update-balance-not-found'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BalancesController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::back()
                ->withInput()
                ->with('error', __('status.balancescontroller.update-balance-failed'));
        }
    }


    /**
     * Delete a specific balance and handle related operations.
     *
     * This method validates the request, fetches the balance details along with its associated account,
     * and performs the deletion within a database transaction. If the balance is successfully deleted,
     * the user is redirected back to the account's edit page with a success message.
     * In case of an error, it logs the error and redirects back to the accounts edit page with an error message.
     *
     * @param Request $request The HTTP request instance containing the balance ID to be deleted.
     * @return RedirectResponse Redirects to the appropriate route with a success or error message.
     *
     * @throws Exception|Throwable If an error occurs during balance deletion, logs the error and redirects with an error message.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validate([
            'balance_id' => 'required|exists:balances,id',
        ]);

        try {
            $balance = Balance::with('account')
                ->findOrFail($request->input('balance_id'));

            if ($balance instanceof Balance) {
                // Has always an account relation if not there is a previous error on a database
                $account = $balance->account;

                // Ensure the balance belongs to the authenticated user's account
                if ($account->is_manual && $account->user_id !== $user->id) {
                    return Redirect::route('profile.accounts.edit')
                        ->with('error', __('status.balancescontroller.delete-balance-not-found'));
                }

                DB::transaction(function () use ($balance) {
                    $balance->delete();
                });

                return Redirect::route('profile.balance.edit', ['id' => $account->code])
                    ->with('success', __('status.balancescontroller.delete-balance-success'));
            }
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BalancesController')->error(
                'Error function destroy()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.balancescontroller.delete-balance-failed'));
        }

        return Redirect::route('profile.accounts.edit')
            ->with('error', __('status.balancescontroller.delete-balance-failed'));
    }
}
