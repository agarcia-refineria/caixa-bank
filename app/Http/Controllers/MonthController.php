<?php

namespace App\Http\Controllers;

class MonthController extends Controller
{
    /**
     * Handles the request to set or retrieve the session month.
     *
     * This method checks if a month parameter is provided. If provided,
     * it updates the session with the formatted month. If not provided,
     * it defaults to using the current month. The method then returns
     * a JSON response indicating success or default behavior.
     *
     * @param string|null $month The month parameter in a recognized date format.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the operation's result.
     */
    public function index($month): \Illuminate\Http\JsonResponse
    {
        session('month', date('m-Y'));

        if ($month) {
            session(['month' => date('m-Y', strtotime($month))]);
            return response()->json('ok');
        }

        return response()->json('default');
    }
}
