<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CategoryAccount;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountsController extends Controller
{
    /**
     * Display the account's page.
     *
     * @return View The view for the account's page.
     */
    public function edit(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        try {
            $accounts = Account::where('user_id', $user->id)
                ->orderBy('order')
                ->get();

            return view('pages.profile.accounts', compact('user', 'accounts'));
        } catch (Exception $e) {
            // Log the error and abort with a 500 status code
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function edit()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            abort(500);
        }
    }

    /**
     * Create a new account and redirect to the account edit page.
     *
     * @param Request $request The incoming HTTP request containing the account details.
     * @return RedirectResponse Redirects to the account edit view with a success status.
     */
    public function create(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $request->validate([
            'newAccount.owner_name' => ['required', 'string', 'max:255'],
            'newAccount.bban' => ['nullable', 'string', 'max:255'],
            'newAccount.iban' => ['required', 'string', 'max:255'],
            'newAccount.institution_id' => ['required', 'exists:institutions,id'],
            'newAccount.status' => ['nullable', 'string'],
        ]);

        $accountData = $request->input('newAccount');

        $user = Auth::user();

        try {
            Account::create([
                'id' => Str::uuid()->toString(),
                'iban' => $accountData['iban'],
                'bban' => $accountData['bban'],
                'status' =>$accountData['status'] ?? 'active',
                'owner_name' => $accountData['owner_name'],
                'institution_id' => $accountData['institution_id'],
                'user_id' => $user->id,
                'type' => Account::$accountTypes['manual'],
                'order' => Account::where('user_id', $user->id)->max('order') + 1,
            ]);

            return redirect()->route('profile.accounts.edit')
                ->with('status', __('status.accountscontroller.create-account-success'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function create()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return redirect()->route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.create-account-failed'));
        }
    }

    /**
     * Update the account details for the authenticated user.
     *
     * @param Request $request The incoming HTTP request containing the updated account details.
     * @return RedirectResponse Redirects to the account edit view with a success status.
     */
    public function update(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $validated = $request->validate([
            'id' => ['required', 'exists:accounts,id'],
        ]);

        $user = Auth::user();

        $key = $request->input('id');

        $validated = array_merge($validated, $request->validate([
            "Account.$key.owner_name" => ['required', 'string', 'max:255'],
            "Account.$key.bban" => ['nullable', 'string', 'max:255'],
            "Account.$key.iban" => ['required', 'string', 'max:255'],
            "Account.$key.institution_id" => ['required', 'exists:institutions,id'],
            "Account.$key.status" => ['nullable', 'string'],
        ]));

        $accountData = $validated['Account'][$key];

        try {
            $account = Account::where('user_id', $user->id)
                ->where('id', $validated['id'])
                ->firstOrFail();

            $account->update([
                'owner_name' => $accountData["owner_name"],
                'bban' => $accountData['bban'],
                'iban' => $accountData['iban'],
                'institution_id' => $accountData['institution_id'],
                'status' => $accountData['status'],
            ]);

            return Redirect::route('profile.accounts.edit')
                ->with('status', __('status.accountscontroller.update-account-success'));
        } catch (ModelNotFoundException $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.update-account-not-found'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.update-account-failed'));
        }
    }

    /**
     * Handle the deletion of a user account.
     *
     * @param Request $request The incoming HTTP request.
     * @param string $id The ID of the account to be deleted.
     * @return RedirectResponse A redirect response to the account's edit page with a status message.
     * @throws Throwable
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $key = array_key_first($request->input('Account'));

        $request->validateWithBag('userDeletion', [
            "Account.$key.password" => ['required', 'current_password'],
        ]);

        try {
            $account = Account::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            DB::beginTransaction();
            try {
                $account->delete();
                DB::commit();
                return Redirect::route('profile.accounts.edit')
                    ->with('status', __('status.accountscontroller.delete-account-success'));
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function destroy()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.delete-account-not-found'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function destroy()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.accounts.edit')
                ->with('error', __('status.accountscontroller.delete-account-failed'));
        }
    }

    /**
     * Reorder the accounts based on the provided IDs.
     *
     * @param Request $request The incoming HTTP request containing the ordered IDs.
     * @return JsonResponse A JSON response indicating the status of the operation.
     * @throws Throwable
     */
    public function reorder(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.reorder-failed')
            ], 500);
        }

        $validated = $request->validate([
            'ids' => 'required|array',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            foreach ($validated['ids'] as $index => $id) {
                Account::where('id', $id)
                    ->where('user_id', $user->id)
                    ->update(['order' => $index]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => __('status.accountscontroller.reorder-success')]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function reorder()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.reorder-failed')
            ], 500);
        }
    }

    /**
     * Process and assign a paysheet to an account.
     *
     * @param Request $request The incoming HTTP request containing the transaction details.
     *
     * @throws Throwable
     */
    public function paysheet(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.paysheet-failed')
            ], 500);
        }

        $validated = $request->validate([
            'paysheet' => 'nullable|exists:transactions,id',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $account = Account::where('user_id', $user->id)
                ->where('id', $validated['account_id'])
                ->firstOrFail();
            $paysheet = Transaction::find($validated['paysheet']);

            $account->paysheet_id = $paysheet?->id;
            $account->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('status.accountscontroller.paysheet-success')
            ]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function paysheet()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.paysheet-failed')
            ], 500);
        }
    }

    /**
     * Disable transactions for an account.
     *
     * @param Request $request The incoming HTTP request containing the account ID.
     * @return JsonResponse Redirects back with a status message.
     */
    public function disableTransactions(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.disable-transactions-failed')
            ], 500);
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'categories' => 'nullable|array'
        ]);

        $user = Auth::user();

        try {
            $account = Account::where('user_id', $user->id)
                ->where('id', $validated['account_id'])
                ->firstOrFail();

            // Reset paysheet_disabled for all categories of the account
            CategoryAccount::where('account_id', $account->code)
                ->update(['paysheet_disabled' => false]);

            foreach ($validated['categories'] ?? [] as $category) {
                if (!is_numeric($category)) {
                    continue;
                }

                // Check if the category already exists for the account
                $existingCategory = CategoryAccount::where('category_id', $category)
                    ->where('account_id', $account->code)
                    ->first();

                if ($existingCategory) {
                    // If it exists, update the paysheet_disabled status
                    $existingCategory->paysheet_disabled = true;
                    $existingCategory->save();
                    continue;
                }

                CategoryAccount::create([
                    'category_id' => $category,
                    'account_id' => $account->code,
                    'paysheet_disabled' => true,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('status.accountscontroller.disable-transactions-success')
            ]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function disableTransactions()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.disable-transactions-failed')
            ], 500);
        }
    }

    /**
     * Apply or remove the application of monthly expenses for the specified account.
     *
     * @param Request $request The incoming HTTP request containing account data and expense application flag.
     *
     * @return JsonResponse The success or error JSON response indicating the operation status.
     *
     * @throws ModelNotFoundException If the specified account does not belong to the authenticated user.
     * @throws Exception|Throwable If an error occurs during the database transaction.
     */
    public function applyExpensesMonthly(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.apply-expenses-monthly-failed')
            ], 500);
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'apply_expenses_monthly' => 'required|boolean',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $account = Account::where('user_id', $user->id)
                ->where('id', $validated['account_id'])
                ->firstOrFail();

            $account->apply_expenses_monthly = $validated['apply_expenses_monthly'];
            $account->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('status.accountscontroller.apply-expenses-monthly-success')
            ]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('AccountsController')->error(
                'Error function applyExpensesMonthly()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('status.accountscontroller.apply-expenses-monthly-failed')
            ], 500);
        }
    }
}
