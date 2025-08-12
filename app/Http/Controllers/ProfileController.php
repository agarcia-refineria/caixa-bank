<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        return view('pages.profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if (!auth()->check()) {
            abort(403);
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('pages.profile.edit')->with('success', __('status.profilecontroller.profile-updated'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            abort(403);
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', __('status.profilecontroller.profile-deleted'));
    }

    public function logs(): View
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = Auth::user();

        $basePath = storage_path('logs/users');
        $userLogs = [];

        if (!File::exists($basePath)) {
            abort(403);
        }

        $users = File::directories($basePath);

        foreach ($users as $userPath) {
            $logFiles = File::files($userPath);

            foreach ($logFiles as $file) {
                $userLogs[] = [
                    'filename' => $file->getFilename(),
                    'content' => File::get($file),
                    'size' => File::size($file),
                    'updated_at' => $file->getMTime(),
                ];
            }
        }

        return view('pages.profile.logs', [
            'user' => $user,
            'userLogs' => $userLogs
        ]);
    }

    public function clearLogs(): RedirectResponse
    {
        if (!auth()->check()) {
            abort(403);
        }

        $filename = request()->input('filename');

        if (!$filename) {
            return Redirect::route('profile.logs')->withErrors(__('status.profilecontroller.logs-clear-failed'));
        }

        $user = Auth::user();
        $logPath = storage_path("logs/users/{$user->email}/{$filename}");

        if (!File::exists($logPath)) {
            return Redirect::route('profile.logs')->withErrors(__('status.profilecontroller.logs-not-found'));
        }

        // Clear the log file
        File::put($logPath, '');
        // Optionally, you can also delete the file if you want to remove it completely
        // File::delete($logPath);

        return Redirect::route('profile.logs')->with('success', __('status.profilecontroller.logs-cleared'));
    }
}
