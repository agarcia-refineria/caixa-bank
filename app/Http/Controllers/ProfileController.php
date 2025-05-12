<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Display the user's bank profile form.
     */
    public function bank(Request $request): View
    {
        $user = auth()->user();
        $bank = Bank::where('user_id', $user->id)->first();

        return view('profile.bank', [
            'user' => $user,
            'bank' => $bank,
        ]);
    }

    /**
     * Display the user's import profile form.
     */
    public function import(Request $request): View
    {
        return view('profile.import', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Display the user's accounts profile form.
     */
    public function accounts(Request $request): View
    {
        $user = auth()->user();
        $accounts = \App\Models\Account::where('user_id', $user->id)->orderBy('order')->get();

        return view('profile.accounts', [
            'user' => $user,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Create a new account for the user.
     */
    public function createAccounts(Request $request): RedirectResponse
    {
        $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'bban' => ['nullable', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
        ]);

        $accountData = $request->all();

        Account::create([
            'id' => time().rand(0, 20534536), // Unique identifier for the account
            'iban' => $accountData['iban'],
            'bban' => $accountData['bban'] ?? '',
            'status' => $accountData['status'] ?? '',
            'owner_name' => $accountData['owner_name'],
            'institution_id' => \auth()->user()->bank->institution_id,
            'user_id' => auth()->user()->id,
            'type' => Account::$accountTypes['manual']
        ]);

        return Redirect::route('profile.accounts.edit')->with('status', 'account-created');
    }

    /**
     * Update the user's account information.
     */
    public function updateAccount(Request $request): RedirectResponse
    {
         $request->validate([
             'id' => ['required'],
             'owner_name' => ['required', 'string', 'max:255'],
             'bban' => ['nullable', 'string', 'max:255'],
             'iban' => ['required', 'string', 'max:255'],
             'status' => ['nullable', 'string', 'max:255'],
         ]);

        $user = Auth::user();
        $account = \App\Models\Account::where('user_id', $user->id)->where('id', $request->id)->first();

        if ($account) {
            $account->owner_name = $request->owner_name;
            $account->bban = $request->bban;
            $account->iban = $request->iban;
            $account->status = $request->status;
            $account->save();
        }

        return Redirect::route('profile.accounts.edit')->with('status', 'account-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroyAccount(Request $request, $id): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        $account = \App\Models\Account::where('user_id', $user->id)->where('id', $id)->first();
        if ($account) {
            $account->delete();
        }
        return Redirect::route('profile.accounts.edit')->with('status', 'account-deleted');
    }

    public function importAccountsCSV(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv_accounts' => ['nullable', 'mimes:csv,txt'],
            'file_xlsx_accounts' => ['nullable', 'mimes:xlsx,xls'],
        ]);

        if (!$request->file('file_csv_accounts') && !$request->file('file_xlsx_accounts')) {
            return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Please upload a CSV file.', 'file_xlsx_accounts' => 'Please upload an XLSX file.']);
        }

        $user = \auth()->user();

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
                    return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Duplicate account ID found: ' . $account->id]);
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
                    return Redirect::route('profile.import.edit')->withErrors(['file_csv_accounts' => 'Duplicate account ID found: ' . $account->id]);
                }

                // Create the account
                $account->save();
            }
        }

        return Redirect::route('profile.import.edit')->with('success', 'Cuentas importadas correctamente');
    }

    public function importTransactionsCSV(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);
        $file = $request->file('file');
        $path = $file->store('csv');

        $file = fopen(storage_path('app/'.$path), 'r');

        $header = fgetcsv($file);

        dd($header);

        return Redirect::route('profile.import.edit')->with('status', 'account-imported');
    }

    public function importBalancesCSV(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);
        $file = $request->file('file');
        $path = $file->store('csv');

        $file = fopen(storage_path('app/'.$path), 'r');

        $header = fgetcsv($file);

        dd($header);

        return Redirect::route('profile.import.edit')->with('status', 'account-imported');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Reorder accounts based on the provided IDs and their indexes.
     *
     * Updates the 'order' attribute of accounts to reflect the provided order.
     */
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        foreach ($request->ids as $index => $id) {
            \App\Models\Account::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the user's schedule settings and associated scheduled tasks.
     *
     * Validates the provided schedule times input against the maximum allowed limit.
     * If the validation fails, redirects the user back to the profile edit page
     * with an error status. Updates the user's schedule times and login execution
     * settings in the database. Clears the user's existing scheduled tasks and
     * repopulates them based on the input times, capped by the maximum schedule times.
     *
     * @param Request $request The incoming HTTP request containing schedule and time data.
     * @return RedirectResponse Redirects to the profile edit page with a status message.
     */
    public function schedule(Request $request): RedirectResponse
    {
        if ($request->schedule_times && $request->schedule_times > \App\Models\ScheduledTasks::$MAX_TIMES) {
            return Redirect::route('profile.edit')->with('status', 'schedule-error');
        }

        $user = Auth::user();
        $user->schedule_times = $request->schedule_times;
        $user->execute_login = $request->execute_login == 'on' ? 1 : 0;
        $user->save();

        $scheduledTasks = \App\Models\ScheduledTasks::where('user_id', $user->id)->truncate();

        if ($request->times) {
            foreach ($request->times as $index => $time) {
                if ($index >= $request->schedule_times) {
                    break;
                }
                $scheduledTask = new \App\Models\ScheduledTasks();
                $scheduledTask->hour = $time;
                $scheduledTask->user_id = $user->id;
                $scheduledTask->save();
            }
        }

        return Redirect::route('profile.edit')->with('status', 'schedule-updated');
    }

    /**
     * Execute the scheduled tasks immediately.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scheduleTasks(Request $request): \Illuminate\Http\JsonResponse
    {
        \Artisan::call('schedule:run');
        return response()->json(['status' => 'excuted']);
    }

    /**
     * Updates or creates a bank record associated with the authenticated user and redirects to the profile edit page with a success status.
     *
     * @return RedirectResponse
     */
    public function bankUpdate(): RedirectResponse
    {
        $user = Auth::user();

        $bank = Bank::where('user_id', $user->id)->first();
        if ($bank) {
            $bank->institution_id = request('institution');
            $bank->save();
        } else {
            $banknew = new Bank();
            $banknew->user_id = $user->id;
            $banknew->institution_id = request('institution');
            $banknew->save();
        }

        return Redirect::route('profile.edit')->with('status', 'bank-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
