<?php

namespace App\Http\Controllers;

class LangController extends Controller
{
    /**
     * Handles the locale setting for the application.
     *
     * Checks if the provided locale is supported and updates the application locale
     * and session with the selected language. Redirects the user back to the previous page.
     *
     * @param string $locale The locale to be set (e.g., 'en', 'es').
     * @return \Illuminate\Http\RedirectResponse Redirects back to the previous page.
     */
    public function index($locale): \Illuminate\Http\RedirectResponse
    {
        if (in_array($locale, ['en', 'es'])) {
            session(['locale' => $locale]);
            \Illuminate\Support\Facades\App::setLocale($locale);
        }
        return redirect()->back();
    }
}
