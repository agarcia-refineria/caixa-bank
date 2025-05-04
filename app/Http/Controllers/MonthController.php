<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    public function index($month)
    {
        session('month', date('m-Y'));

        if ($month) {
            session(['month' => date('m-Y', strtotime($month))]);
            return response()->json('ok');
        }

        return response()->json('default');
    }
}
