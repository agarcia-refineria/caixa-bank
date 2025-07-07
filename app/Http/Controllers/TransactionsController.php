<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class TransactionsController extends Controller
{
    /**
     * Display the edit form for a specific manual account and its transactions.
     *
     * This method fetches the account associated with the provided account ID,
     * ensuring the account belongs to the authenticated user and is of manual type.
     * If the account is not manual, redirects back to the accounts edit route
     * with an appropriate error message. If the validation passes, retrieves
     * associated transactions for the account and returns the edit view.
     *
     * @param string $accountId The identifier of the account to be edited.
     * @return View|RedirectResponse The view for editing transactions of the account
     *                               or a redirect response with an error message.
     */
    public function edit(string $accountId): View|RedirectResponse
    {
        if (!auth()->check()) {
            abort(403);
        }

        $account = Account::where('user_id', Auth::id())
            ->findOrFail($accountId);

        $transactions = $account->transactions()
            ->orderBy('bookingDate', 'desc')
            ->get();

        return view('pages.profile.transactions.edit', [
            'user' => auth()->user(),
            'account' => $account,
            'transactions' => $transactions
        ]);
    }

    /**
     * Create a new transaction for a manual account.
     *
     * This method validates the incoming request data to ensure all required
     * fields are properly provided and comply with specific rules. If valid,
     * it fetches the associated manual account and creates a new transaction
     * entry in the database. On success, redirects to the transaction edit page
     * with a success message. If an exception occurs, redirects back with an
     * error message.
     *
     * @param Request $request The incoming HTTP request containing transaction details.
     * @return RedirectResponse A redirect to the transaction edit route, carrying
     *                          either a success or an error message.
     */
    public function create(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validateWithBag('transactionCreate', [
            'account_id' => 'required|exists:accounts,id'
        ]);

        // Ensure the account is a manual account and belongs to the user
        $account = Account::onlyManual()
            ->where('user_id', $user->id)
            ->findOrFail($validated['account_id']);

        if (!$account) {
            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.transactionscontroller.account-not-found'));
        }

        $validated = $request->validateWithBag('transactionCreate', [
            'newTransaction.bookingDate' => 'required|date',
            'newTransaction.transactionAmount_amount' => 'required|numeric',
            'newTransaction.valueDate' => 'nullable|date',
            'newTransaction.transactionAmount_currency' => 'nullable|string|size:3',
            'newTransaction.entryReference' => 'nullable|string|max:255',
            'newTransaction.checkId' => 'nullable|string|max:255',
            'newTransaction.remittanceInformationUnstructured' => 'nullable|string|max:1000',
            'newTransaction.bankTransactionCode' => 'nullable|string|max:255',
            'newTransaction.proprietaryBankTransactionCode' => 'nullable|string|max:255',
            'newTransaction.internalTransactionId' => 'nullable|string|max:255',
            'newTransaction.debtorName' => 'nullable|string|max:255',
            'newTransaction.debtorAccount' => 'nullable|string|max:255',
        ]);

        try {
            $transactionData = $validated['newTransaction'];

            Transaction::create([
                'id' => Str::uuid(),
                'entryReference' => $transactionData['entryReference'] ?? null,
                'checkId' => $transactionData['checkId'] ?? null,
                'bookingDate' => $transactionData['bookingDate'],
                'valueDate' => $transactionData['valueDate'] ?? $transactionData['bookingDate'],
                'transactionAmount_amount' => $transactionData['transactionAmount_amount'],
                'transactionAmount_currency' => $transactionData['transactionAmount_currency'] ?? 'EUR',
                'remittanceInformationUnstructured' => Transaction::setRemittanceInformationUnstructuredFormat($transactionData['remittanceInformationUnstructured']),
                'bankTransactionCode' => $transactionData['bankTransactionCode'] ?? null,
                'proprietaryBankTransactionCode' => $transactionData['proprietaryBankTransactionCode'] ?? null,
                'internalTransactionId' => $transactionData['internalTransactionId'] ?? null,
                'debtorName' => $transactionData['debtorName'] ?? null,
                'debtorAccount' => $transactionData['debtorAccount'] ?? null,
                'account_id' => $account->code,
                'category_id' => Transaction::getCategoryId($transactionData['remittanceInformationUnstructured'] ?? null),
            ]);

            return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                ->with('success', __('status.transactionscontroller.transaction-created'));

        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('TransactionsController')->error(
                'Error function create()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.transactionscontroller.transaction-creation-failed'));
        }
    }

    /**
     * Update a specific transaction associated with a manual account.
     *
     * Validates the incoming request data, ensuring the provided transaction and account IDs exist,
     * and that the required fields meet their respective constraints. Once validated, attempts to
     * update the transaction with the provided input data while ensuring the account is of manual type.
     * If the operation succeeds, redirects to the transaction edit page with a success message.
     * In the event of an error, logs the exception and rolls back any changes, redirecting with an error message.
     *
     * @param Request $request The incoming HTTP request containing transaction and account details.
     * @return RedirectResponse A redirection to the transaction edit page with success or error feedback.
     * @throws Throwable
     */
    public function update(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validateWithBag('transactionUpdate', [
            'transaction_id' => 'required|exists:transactions,id',
            "account_id" => 'required|exists:accounts,id',
        ]);

        $key = $request->input('transaction_id');

        $request->validateWithBag('transactionUpdate', [
            "Transaction.$key.entryReference" => 'nullable|string|max:255',
            "Transaction.$key.checkId" => 'nullable|string|max:255',
            "Transaction.$key.bookingDate" => 'required|date',
            "Transaction.$key.valueDate" => 'nullable|date',
            "Transaction.$key.transactionAmount_amount" => 'required|numeric',
            "Transaction.$key.transactionAmount_currency" => 'nullable|string|size:3',
            "Transaction.$key.remittanceInformationUnstructured" => 'nullable|string|max:1000',
            "Transaction.$key.bankTransactionCode" => 'nullable|string|max:255',
            "Transaction.$key.proprietaryBankTransactionCode" => 'nullable|string|max:255',
            "Transaction.$key.internalTransactionId" => 'nullable|string|max:255',
            "Transaction.$key.debtorName" => 'nullable|string|max:255',
            "Transaction.$key.debtorAccount" => 'nullable|string|max:255',
        ]);

        $transactionData = $request->input("Transaction.$key");

        try {
            // Ensure the account is a manual account and belongs to the user
            $account = Account::onlyManual()
                ->where('user_id', $user->id)
                ->findOrFail($request->input('account_id'));

            // Ensure the transaction belongs to the account
            $transaction = Transaction::where('account_id', $account->code)
                ->findOrFail($request->input('transaction_id'));

            if (!$transaction) {
                return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                    ->with('error', __('status.transactionscontroller.transaction-not-found'));
            }

            DB::beginTransaction();

            $transaction->update([
                'entryReference' => $transactionData['entryReference'],
                'checkId' => $transactionData['checkId'],
                'bookingDate' => $transactionData['bookingDate'],
                'valueDate' => $transactionData['valueDate'] ?? $transactionData['bookingDate'],
                'transactionAmount_amount' => $transactionData['transactionAmount_amount'],
                'transactionAmount_currency' => $transactionData['transactionAmount_currency'] ?? 'EUR',
                'remittanceInformationUnstructured' => Transaction::setRemittanceInformationUnstructuredFormat($transactionData['remittanceInformationUnstructured']),
                'bankTransactionCode' => $transactionData['bankTransactionCode'],
                'proprietaryBankTransactionCode' => $transactionData['proprietaryBankTransactionCode'],
                'internalTransactionId' =>$transactionData['internalTransactionId'],
                'debtorName' => $transactionData['debtorName'],
                'debtorAccount' => $transactionData['debtorAccount'],
                'account_id' => $account->code,
                'category_id' => Transaction::getCategoryId($transactionData['remittanceInformationUnstructured']),
            ]);

            DB::commit();

            return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                ->with('success', __('status.transactionscontroller.transaction-updated'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('TransactionsController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return Redirect::route('profile.transaction.edit', ['id' => $request->input('account_id')])
                ->with('error', __('status.transactionscontroller.transaction-update-failed'));
        }
    }

    /**
     * Delete a transaction.
     *
     * This method validates the incoming request data to ensure the transaction ID and account ID are provided
     * and exist in the database. If valid, it fetches the transaction and account,
     * then deletes the transaction within a database transaction.
     * On success, redirects to the transaction edit page with a success message.
     * If an error occurs, logs the exception and redirects back with an error message.
     *
     * @return RedirectResponse A redirection to the transaction edit page with success or error feedback.
     *
     * @throws Throwable
     */
    public function destroy(): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = request()->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $account = Account::onlyManual()
            ->where('user_id', $user->id)
            ->findOrFail($validated['account_id']);

        $transaction = Transaction::where('account_id', $account->code)
            ->findOrFail($validated['transaction_id']);

        if (!$transaction) {
            return Redirect::route('profile.transaction.edit', ['id' => $account->code])
                ->with('error', __('status.transactionscontroller.transaction-not-found'));
        }

        DB::transaction(function () use ($transaction) {
            $transaction->delete();
        });

        return Redirect::route('profile.transaction.edit', ['id' => $account->code])
            ->with('success', __('status.transactionscontroller.transaction-deleted'));
    }
}
