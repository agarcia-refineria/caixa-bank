<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ImportController extends Controller
{
    /**
     * Muestra la vista de importación del perfil del usuario.
     *
     * @return View
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

    public function accounts(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_accounts' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_accounts' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_accounts') && !$request->file('file_xlsx_accounts')) {
            return Redirect::route('pages.profile.import.edit')->withErrors(['file_csv_accounts' => 'Please upload a CSV file.', 'file_xlsx_accounts' => 'Please upload an XLSX file.']);
        }

        $user = Auth::user();

        $file_csv = $request->file('file_csv_accounts');
        $file_xlsx = $request->file('file_xlsx_accounts');

        if ($file_csv) {
            $path = $file_csv->store('/import/csv');
            $file = fopen(storage_path('app/'.$path), 'r');
            $header = fgetcsv($file);
            $data = [];
            while (($row = fgetcsv($file)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($file);

            foreach ($data as $row) {
                $account = new Account();
                $account->id = $row["ID"];
                $account->name = $row['Name'];
                $account->iban = $row['IBAN'];
                $account->bban = $row['BBAN'] ?? '';
                $account->status = $row['Status'] ?? '';
                $account->owner_name = $row['Owner Name'];
                $account->created = Carbon::createFromFormat('d-m-Y H:i:s', $row['Created']);
                $account->last_accessed = Carbon::createFromFormat('d-m-Y H:i:s', $row['Last Accessed']);
                $account->institution_id = $user->bank->institution_id;
                $account->user_id = $user->id;
                $account->type = Account::$accountTypes['manual'];

                // Check if the account already exists
                $existingAccount = Account::where('user_id', $user->id)->where('id', $account->id)->first();
                if ($existingAccount) {
                    return Redirect::route('pages.import.edit')->withErrors(['file_csv_accounts' => 'Duplicate account ID found: ' . $account->id]);
                }

                // Create the account
                $account->save();
            }
        }

        if ($file_xlsx) {
            $path = $file_xlsx->store('/import/xlsx');
            $file = fopen(storage_path('app/'.$path), 'r');
            $header = fgetcsv($file);
            $data = [];
            while (($row = fgetcsv($file)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($file);

            foreach ($data as $row) {
                $account = new Account();
                $account->id = $row["ID"];
                $account->name = $row['Name'];
                $account->iban = $row['IBAN'];
                $account->bban = $row['BBAN'] ?? '';
                $account->status = $row['Status'] ?? '';
                $account->owner_name = $row['Owner Name'];
                $account->created = Carbon::createFromFormat('d-m-Y H:i:s', $row['Created']);
                $account->last_accessed = Carbon::createFromFormat('d-m-Y H:i:s', $row['Last Accessed']);
                $account->institution_id = $user->bank->institution_id;
                $account->user_id = $user->id;
                $account->type = Account::$accountTypes['manual'];

                // Check if the account already exists
                $existingAccount = Account::where('user_id', $user->id)->where('id', $account->id)->first();
                if ($existingAccount) {
                    return Redirect::route('pages.profile.import.edit')->withErrors(['file_csv_accounts' => 'Duplicate account ID found: ' . $account->id]);
                }

                // Create the account
                $account->save();
            }
        }

        return Redirect::route('pages.profile.import.edit')->with('success', 'Cuentas importadas correctamente');
    }

    public function transaction(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);
        $file = $request->file('file');
        $path = $file->store('csv');

        $file = fopen(storage_path('app/'.$path), 'r');

        $header = fgetcsv($file);

        dd($header);

        return Redirect::route('pages.profile.import.edit')->with('status', 'account-imported');
    }

    public function balances(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);
        $file = $request->file('file');
        $path = $file->store('csv');

        $file = fopen(storage_path('app/'.$path), 'r');

        $header = fgetcsv($file);

        dd($header);

        return Redirect::route('pages.profile.import.edit')->with('status', 'account-imported');
    }
}
