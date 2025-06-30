<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\ScheduledTasks;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class BankController extends Controller
{
    /**
     * Display the bank edit form.
     *
     * @return View
     *
     */
    public function edit(): View
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        return view('pages.profile.bank', [
            'user' => $user,
            'bank' => $user->bank,
        ]);
    }


    /**
     * Update the user's bank details.
     *
     * @return RedirectResponse
     *
     */
    public function update(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return Redirect::route('login');
        }

        $user->update([
            'NORDIGEN_SECRET_ID' => request('NORDIGEN_SECRET_ID') ? encrypt(request('NORDIGEN_SECRET_ID')) : $user->NORDIGEN_SECRET_ID,
            'NORDIGEN_SECRET_KEY' => request('NORDIGEN_SECRET_KEY') ? encrypt(request('NORDIGEN_SECRET_KEY')) : $user->NORDIGEN_SECRET_KEY,
        ]);
        $user->save();

        try {
            if (\request()->has('institution')) {
                $validated = request()->validate([
                    'institution' => ['required', 'exists:institutions,id'],
                ]);

                Bank::updateOrCreate(
                    ['user_id' => $user->id],
                    ['institution_id' => $validated['institution']]
                );

                return Redirect::route('profile.bank.edit')
                    ->with('status', __('status.bankcontroller.update-account-success'));
            }

            return Redirect::route('profile.bank.edit')
                ->with('status', __('status.bankcontroller.update-account-success'));
        } catch (Exception $e) {
            Log::error('Error actualizando banco del usuario: ' . $e->getMessage());

            return Redirect::route('profile.bank.edit')
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
        $validated = $request->validate([
            'chars' => ['required', 'in:'. implode(',', User::$charsTypes)],
        ]);

        try {
            $user = Auth::user();
            $user->update(['chars' => $validated['chars']]);

            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.chars-updated'));
        } catch (Exception $e) {
            Log::error('Error al actualizar los caracteres del banco: ' . $e->getMessage());
            return redirect()->route('profile.bank.edit')->with('error', __('status.bankcontroller.chars-error'));
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
        $validated = $request->validate([
            'theme' => ['required'],
        ]);

        try {
            $user = Auth::user();
            $user->update(['theme' => $validated['theme']]);

            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.theme-updated'));
        } catch (Exception $e) {
            Log::error('Error al actualizar el tema del banco: ' . $e->getMessage());
            return redirect()->route('profile.bank.edit')->with('error', __('status.bankcontroller.theme-error'));
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
        $validated = $request->validate([
            'lang' => ['required', 'string', 'max:2'],
        ]);

        try {
            $user = Auth::user();
            $user->update(['lang' => $validated['lang']]);

            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.lang-updated'));
        } catch (Exception $e) {
            Log::error('Error al actualizar el idioma del banco: ' . $e->getMessage());
            return redirect()->route('profile.bank.edit')->with('error', __('status.bankcontroller.lang-error'));
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
        $validated = $request->validate([
            'schedule_times' => ['required', 'integer', 'max:' . ScheduledTasks::$MAX_TIMES],
            'times.*' => ['nullable', 'date_format:H:i'],
            'execute_login' => ['nullable', 'string']
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

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
            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.schedule-updated'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al programar tareas: ' . $e->getMessage());
            return redirect()->route('profile.bank.edit')->with('status', __('status.bankcontroller.schedule-error'));
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
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'No autorizado'], 401);
            }

            dispatch(function () {
                Artisan::call('schedule:run');
            })->afterResponse();

            return response()->json(['status' => 'executed']);
        } catch (Exception $e) {
            Log::error('Error ejecutando tareas programadas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al ejecutar tareas programadas'], 500);
        }
    }
}
