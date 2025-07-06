<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1);
        if (in_array($locale, config('app.supported_locales'))) {
            App::setLocale($locale);
        } else {
            // If the locale is not supported, you can redirect to a default locale of the user's choice
            App::setLocale(Auth::user()->lang);
        }

        return $next($request);
    }
}
