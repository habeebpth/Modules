<?php

use Illuminate\Support\Facades\Route;
use Modules\Travels\Http\Controllers\TravelSettingController;
use Modules\Travels\Http\Controllers\TravelAirlineSettingController;
use Modules\Travels\Http\Controllers\TravelDestinationSettingController;
use Modules\Travels\Http\Controllers\TravelvehicletypeSettingController;
use Modules\Travels\Http\Controllers\TravelvehicleSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('travels', TravelsController::class)->names('travels');
});
// Admin routes
Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    // TravelSetting
    Route::resource('travel-settings', TravelSettingController::class);
    //TravelSetting airline
    Route::resource('travel-airline-settings', TravelAirlineSettingController::class);
    Route::post('/travel-airline-settings/update-status', [TravelAirlineSettingController::class, 'updateStatus'])->name('travel-airline-settings.update-status');
    //TravelSetting destination
    Route::resource('destination-settings', TravelDestinationSettingController::class);
    Route::post('/destination-settings/update-status', [TravelDestinationSettingController::class, 'updateStatus'])->name('destination-settings.update-status');
    //TravelSetting vehicletype
    Route::resource('vehicletype-settings', TravelvehicletypeSettingController::class);
    Route::post('/vehicletype-settings/update-status', [TravelvehicletypeSettingController::class, 'updateStatus'])->name('vehicletype-settings.update-status');
    //TravelSetting vehicle
    Route::resource('vehicle-settings', TravelvehicleSettingController::class);
    Route::post('/vehicle-settings/update-status', [TravelvehicleSettingController::class, 'updateStatus'])->name('vehicle-settings.update-status');
});
