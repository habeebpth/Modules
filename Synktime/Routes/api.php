<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Synktime\Http\Controllers\Api\STTransactionApiController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('synktime', fn (Request $request) => $request->user())->name('synktime');
});
Route::group([
    'middleware' => 'auth:api2',  // same as your main 'app' group
    'prefix' => 'app/synktime',   // consistent API structure
], function () {
    Route::post('store-transaction', [STTransactionApiController::class, 'store']);
    Route::get('get_punch_status', [STTransactionApiController::class, 'getPunchStatus']);
});
