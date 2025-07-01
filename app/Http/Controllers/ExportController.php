<?php

namespace App\Http\Controllers;

use App\Exports\AccountsExport;
use App\Exports\BalancesExport;
use App\Exports\TransactionsExport;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{

    /**
     * Displays the profile import page for the authenticated user.
     *
     * This method checks whether a user is authenticated before rendering the view.
     * If the user is not authenticated, a 401 Unauthorized error is returned.
     * When authenticated, the user's information is passed to the view.
     *
     * @return View The rendered view of the profile import page with user details.
     */
    public function show(): View
    {
        if (!auth()->check()) {
            abort(401);
        }

        return view('pages.profile.export', [
            'user' => auth()->user(),
        ]);
    }


    /**
     * Handles the exporting accounts.
     *
     * @param Request $request The HTTP request instance containing the uploaded files for accounts.
     *
     * @return BinaryFileResponse|RedirectResponse Redirects back to the profile import edit page with success or error messages for the import process.
     */
    public function accounts(Request $request, string $type): BinaryFileResponse|RedirectResponse
    {
        try {
            return Excel::download(new AccountsExport(), 'accounts_'.date('d_m_Y').'.'.$type, $this->getType($type));
        } catch (Exception $e) {
            return Redirect::route('profile.export.edit')->withErrors(['file_csv_accounts' => 'Error reading CSV file: ' . $e->getMessage()]);
        }
    }

    /**
     * Handles the exporting transactions.
     *
     * @param Request $request The HTTP request instance containing the uploaded files.
     *
     * @return BinaryFileResponse|RedirectResponse Redirects back to the profile import edit page with success or error messages.
     */
    public function transaction(Request $request, string $type): BinaryFileResponse|RedirectResponse
    {
        try {
            return Excel::download(new TransactionsExport(), 'transactions_'.date('d_m_Y').'.'.$type, $this->getType($type));
        } catch (Exception $e) {
            return Redirect::route('profile.export.edit')->withErrors(['file_csv_transactions' => 'Error reading CSV file: ' . $e->getMessage()]);
        }
    }

    /**
     * Handles the exporting balances.
     *
     * @param Request $request The HTTP request instance containing the uploaded files.
     *
     * @return BinaryFileResponse|RedirectResponse Redirects back to the profile import edit page with success or error messages.
     */
    public function balances(Request $request, string $type): BinaryFileResponse|RedirectResponse
    {
        try {
            return Excel::download(new BalancesExport(), 'balances_'.date('d_m_Y').'.'.$type, $this->getType($type));
        } catch (Exception $e) {
            return Redirect::route('profile.export.edit')->withErrors(['file_csv_balances' => 'Error reading CSV file: ' . $e->getMessage()]);
        }
    }

    public function getType(string $type): string
    {
        return match ($type) {
            'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            default => \Maatwebsite\Excel\Excel::CSV,
        };
    }
}
