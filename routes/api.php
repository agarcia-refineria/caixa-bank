<?php

use App\Http\Controllers\Api\DatatableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/datatable/accounts', [DatatableController::class, 'accounts'])->name('api.datatable.accounts');
Route::get('/api/datatable/balances/{id?}', [DatatableController::class, 'balances'])->name('api.datatable.balances');
Route::get('/api/datatable/transactions/{id?}', [DatatableController::class, 'transactions'])->name('api.datatable.transactions');
