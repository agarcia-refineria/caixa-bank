<?php

namespace App\Http\Controllers;

use App\Imports\AccountImport;
use App\Imports\BalanceImport;
use App\Imports\TransactionImport;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

        $user = Auth::user();

        return view('pages.profile.import', [
            'user' => $user,
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
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validate([
            'file_accounts' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $request->file('file_accounts');

        try {
            $fullPath = $this->moveUploadedFile($file);
            Excel::import(new AccountImport(), $fullPath, null, \Maatwebsite\Excel\Excel::CSV);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('ImportController')->error(
                'Error function accounts()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.import.edit')
                ->withErrors(['file_accounts' => __('status.exportcontroller.error-reading-file')]);
        }

        return Redirect::route('profile.import.edit')
            ->with('success', __('status.importcontroller.accounts-imported'));
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
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validate([
            'file_transactions' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $request->file('file_transactions');

        try {
            $fullPath = $this->moveUploadedFile($file);
            Excel::import(new TransactionImport(), $fullPath);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('ImportController')->error(
                'Error function transaction()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.import.edit')
                ->withErrors(['file_transactions' =>  __('status.exportcontroller.error-reading-file')]);
        }

        return Redirect::route('profile.import.edit')->with('success', __('status.importcontroller.transactions-imported'));
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
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $request->validate([
            'file_balances' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $file = $request->file('file_balances');

        try {
            $fullPath = $this->moveUploadedFile($file);
            Excel::import(new BalanceImport(), $fullPath);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('ImportController')->error(
                'Error function balances()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.import.edit')
                ->withErrors(['file_balances' =>  __('status.exportcontroller.error-reading-file')]);
        }

        return Redirect::route('profile.import.edit')->with('success', __('status.importcontroller.balances-imported'));
    }

    private function moveUploadedFile(UploadedFile $file): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $destinationPath = storage_path('app/tmp');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);

        return $destinationPath . '/' . $fileName;
    }
}
