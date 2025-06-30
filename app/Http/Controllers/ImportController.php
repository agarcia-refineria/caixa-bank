<?php

namespace App\Http\Controllers;

use App\Imports\AccountImport;
use App\Imports\BalanceImport;
use App\Imports\TransactionImport;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
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

        return view('pages.profile.import', [
            'user' => auth()->user(),
        ]);
    }


    /**
     * Handles the uploading and importing of account files.
     *
     * This method validates the uploaded files to ensure they are in either CSV or XLSX format.
     * If no files are provided, it redirects back with appropriate validation error messages.
     * Upon successful file upload, the method attempts to import the files using the Maatwebsite Excel package.
     * Exceptions during the import process are caught and result in a redirection with detailed error messages for each file type.
     *
     * @param Request $request The HTTP request instance containing the uploaded files for accounts.
     *
     * @return RedirectResponse Redirects back to the profile import edit page with success or error messages for the import process.
     */
    public function accounts(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_accounts' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_accounts' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_accounts') && !$request->file('file_xlsx_accounts')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Please upload a CSV file.', 'file_xlsx_accounts' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_accounts');
        $file_xlsx = $request->file('file_xlsx_accounts');

        if ($file_csv) {
            try {
                Excel::import(new AccountImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new AccountImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_accounts' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'Cuentas importadas correctamente');
    }

    /**
     * Handles the uploading and importing of transaction files.
     *
     * The method validates the uploaded files to ensure they are either CSV or XLSX.
     * If no files are uploaded, it redirects back with validation errors.
     * Upon successful file upload, it attempts to import the files using the Maatwebsite Excel package.
     * In case of errors during the import process, it captures the exception and redirects with an error message.
     *
     * @param Request $request The HTTP request instance containing the uploaded files.
     *
     * @return RedirectResponse Redirects back to the profile import edit page with success or error messages.
     */
    public function transaction(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_transactions' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_transactions' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_transactions') && !$request->file('file_xlsx_transactions')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_transactions' => 'Please upload a CSV file.', 'file_xlsx_transactions' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_transactions');
        $file_xlsx = $request->file('file_xlsx_transactions');

        if ($file_csv) {
            try {
                Excel::import(new TransactionImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_transactions' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new TransactionImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_transactions' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'account-imported');
    }

    /**
     * Handles the uploading and importing of balance files.
     *
     * This method validates the uploaded files to ensure they are either in CSV or XLSX format.
     * If no files are provided in the request, it redirects back with validation errors.
     * If files are successfully uploaded, the method processes them using the Maatwebsite Excel package.
     * Any errors encountered during the import process are caught and returned as error messages.
     *
     * @param Request $request The HTTP request instance containing the uploaded files.
     *
     * @return RedirectResponse Redirects back to the profile import edit page with success or error messages.
     */
    public function balances(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_balances' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_balances' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_balances') && !$request->file('file_xlsx_balances')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_balances' => 'Please upload a CSV file.', 'file_xlsx_balances' => 'Please upload an XLSX file.']);
        }

        $file_csv = $request->file('file_csv_balances');
        $file_xlsx = $request->file('file_xlsx_balances');

        if ($file_csv) {
            try {
                Excel::import(new BalanceImport(), $file_csv, null, \Maatwebsite\Excel\Excel::CSV);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_csv_balances' => 'Error reading CSV file: ' . $e->getMessage()]);
            }
        }

        if ($file_xlsx) {
            try {
                Excel::import(new BalanceImport(), $file_xlsx, null, \Maatwebsite\Excel\Excel::XLSX);
            } catch (Exception $e) {
                return Redirect::route('profile.import.edit')->withErrors(['file_xlsx_balances' => 'Error reading XLSX file: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'account-imported');
    }
}
