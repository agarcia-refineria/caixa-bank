<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\Request;

class LangController extends Controller
{
    public function index($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            session(['locale' => $locale]);
            \Illuminate\Support\Facades\App::setLocale($locale);
        }
        return redirect()->back();
    }
}
