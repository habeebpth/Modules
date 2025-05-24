<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HotelManagement\Http\Controllers\HMCheckinController;

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
    Route::get('hotelmanagement', fn (Request $request) => $request->user())->name('hotelmanagement');
});
// Guest API Routes (Public access)
Route::get('/guests', [HMCheckinController::class, 'fetchGuests'])->name('hmcheckin.api.guests');
Route::get('/guests/mobile', [HMCheckinController::class, 'searchGuests']);
