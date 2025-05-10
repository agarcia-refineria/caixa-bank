<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    public function bankEdit(Request $request): View
    {
        $user = auth()->user();
        $bank = Bank::where('user_id', $user->id)->first();

        return view('profile.bank', [
            'user' => $user,
            'bank' => $bank,
        ]);
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
