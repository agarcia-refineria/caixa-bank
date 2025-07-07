<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use App\Models\UserInstitution;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\ScheduledTasks;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ConfigurationController extends Controller
{
    /**
     * Display the bank edit form.
     *
     * @return View
     *
     */
    public function edit(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        return view('pages.profile.configuration', [
            'user' => $user,
            'institutions' => $user->institutions()->orderBy('name')->get()
        ]);
    }

    public function viewApi(): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        // Decrypt the API keys
        $secretId = $user->NORDIGEN_SECRET_ID ? decrypt($user->NORDIGEN_SECRET_ID) : '';
        $secretKey = $user->NORDIGEN_SECRET_KEY ? decrypt($user->NORDIGEN_SECRET_KEY) : '';

        return Redirect::route('profile.configuration.edit')
            ->with('success', __('status.bankcontroller.view-api-keys'))
            ->with('secret_id', $secretId)
            ->with('secret_key', $secretKey);
    }


    /**
     * Update the user's bank details.
     *
     * @return RedirectResponse
     *
     */
    public function update(): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $user->update([
            'NORDIGEN_SECRET_ID' => request('NORDIGEN_SECRET_ID') ? encrypt(request('NORDIGEN_SECRET_ID')) : $user->NORDIGEN_SECRET_ID,
            'NORDIGEN_SECRET_KEY' => request('NORDIGEN_SECRET_KEY') ? encrypt(request('NORDIGEN_SECRET_KEY')) : $user->NORDIGEN_SECRET_KEY,
        ]);
        $user->save();

        try {
            // Delete existing user institutions
            UserInstitution::where('user_id', $user->id)->whereNotIn('institution_id', request('institutions') ?? [])->delete();

            if (\request()->has('institutions')) {
                $institutions = request('institutions');

                foreach ($institutions as $institutionId) {
                    // Skip if the institution does not exist
                    $existingInstitution = Institution::find($institutionId);
                    if (!$existingInstitution) {
                        continue;
                    }

                    UserInstitution::updateOrCreate([
                        'user_id' => $user->id,
                        'institution_id' => $institutionId,
                    ]);
                }

                return Redirect::route('profile.configuration.edit')
                    ->with('success', __('status.bankcontroller.update-account-success'));
            }

            return Redirect::route('profile.configuration.edit')
                ->with('success', __('status.bankcontroller.update-account-success'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function update()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return Redirect::route('profile.configuration.edit')
                ->with('error', __('status.bankcontroller.update-account-failed'));
        }
    }

    /**
     * Updates the user's bank characters based on the provided input.
     *
     * Validates the request to ensure the 'chars' field is required and matches
     * the allowed character types. If validation passes, the authenticated user's
     * characters are updated in the database. Upon success, a redirect response
     * is returned with a success status.
     *
     * If an exception occurs during the update process, an error message is logged
     * and the user is redirected back with an error status.
     *
     * @param Request $request Incoming request containing the 'chars' field.
     * @return RedirectResponse Redirect response indicating success or error.
     */
    public function chars(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'chars' => ['required', 'in:'. implode(',', User::$charsTypes)],
        ]);

        try {
            $user->update(['chars' => $validated['chars']]);

            return redirect()->route('profile.configuration.edit')->with('success', __('status.bankcontroller.chars-updated'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function chars()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return redirect()->route('profile.configuration.edit')->with('error', __('status.bankcontroller.chars-error'));
        }
    }

    /**
     * Updates the user's selected bank theme based on the provided input.
     *
     * Validates the request to ensure that the 'theme' field is required. If validation
     * is successful, updates the authenticated user's theme preference in the database.
     * Returns a redirect response with a success status if the update is completed.
     *
     * In the event of an exception during the update, an error message is logged, and the
     * user is redirected back with an error status.
     *
     * @param Request $request The incoming request containing the 'theme' field.
     * @return RedirectResponse Redirect response indicating the outcome of the operation.
     */
    public function theme(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'theme' => ['required'],
        ]);

        try {
            $user->update(['theme' => $validated['theme']]);

            return redirect()->route('profile.configuration.edit')->with('success', __('status.bankcontroller.theme-updated'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function theme()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return redirect()->route('profile.configuration.edit')->with('error', __('status.bankcontroller.theme-error'));
        }
    }

    /**
     * Updates the user's preferred bank language based on the provided input.
     *
     * Validates the request to ensure the 'lang' field is required, is a string,
     * and does not exceed two characters. If validation is successful, the authenticated
     * user's language preference is updated in the database. Upon successful update,
     * a redirect response is returned with a success status.
     *
     * If an error occurs during the update process, an error message is logged
     * and the user is redirected back with an error status.
     *
     * @param Request $request Incoming request containing the 'lang' field.
     * @return RedirectResponse Redirect response indicating success or failure.
     */
    public function lang(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'lang' => ['required', 'string', 'max:2'],
        ]);

        try {
            $user->update(['lang' => $validated['lang']]);

            return redirect()->route('profile.configuration.edit')->with('success', __('status.bankcontroller.lang-updated'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function lang()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return redirect()->route('profile.configuration.edit')->with('error', __('status.bankcontroller.lang-error'));
        }
    }

    /**
     * Schedule tasks for the authenticated user.
     *
     * Validates the incoming request data, updates the user's schedule configuration,
     * and manages the scheduling tasks. Rolls back changes and logs an error
     * if an exception occurs during the process.
     *
     * @param Request $request The incoming HTTP request containing the scheduling data.
     * @return RedirectResponse A redirection to the profile edit page with a status message.
     *
     * @throws Exception|Throwable If an error occurs during the scheduling process.
     */
    public function schedule(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'schedule_times' => ['required', 'integer', 'max:' . ScheduledTasks::$MAX_TIMES],
            'times.*' => ['nullable', 'date_format:H:i'],
            'execute_login' => ['nullable', 'string']
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'schedule_times' => $validated['schedule_times'],
                'execute_login' => ($validated['execute_login'] ?? '') === 'on' ? 1 : 0,
            ]);

            ScheduledTasks::where('user_id', $user->id)->delete();

            if (!empty($validated['times'])) {
                $tasksToCreate = collect(array_slice($validated['times'], 0, $validated['schedule_times']))
                    ->filter(fn($time) => !is_null($time))
                    ->map(fn($time) => [
                        'hour' => $time,
                        'user_id' => $user->id,
                        'created_at' => now()
                    ])
                    ->values()
                    ->toArray();

                if (!empty($tasksToCreate)) {
                    ScheduledTasks::insert($tasksToCreate);
                }
            }

            DB::commit();
            return redirect()->route('profile.configuration.edit')->with('success', __('status.bankcontroller.schedule-updated'));
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function schedule()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            DB::rollBack();
            return redirect()->route('profile.configuration.edit')->with('error', __('status.bankcontroller.schedule-error'));
        }
    }

    /**
     * Execute scheduled tasks.
     *
     * This method checks the user's authentication status and attempts to trigger
     * the Laravel schedule commands asynchronously. Logs an error if an exception
     * occurs during execution.
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function scheduleTasks(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['status' => 'error', 'message' => __('status.bankcontroller.schedule-error')], 401);
        }

        $user = Auth::user();

        try {
            dispatch(function () {
                Artisan::call('schedule:run');
            })->afterResponse();

            return response()->json(['status' => 'success', 'message' => __('status.bankcontroller.schedule-updated')]);
        } catch (Exception $e) {
            $user->getCustomLoggerAttribute('BankController')->error(
                'Error function scheduleTasks()',
                [
                    'message' => $e->getMessage() ?: 'No message provided',
                    'trace' => $e->getTraceAsString() ?: 'No trace available',
                ]
            );

            return response()->json(['status' => 'error', 'message' => __('status.bankcontroller.schedule-error')], 500);
        }
    }
}
