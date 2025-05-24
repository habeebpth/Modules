<?php

use Illuminate\Support\Facades\Route;
use Modules\DWC\Http\Controllers\DWCController;
use Modules\DWC\Http\Controllers\DwcGuestsController;
use Modules\DWC\Http\Controllers\DwcHotelReservationController;
use Modules\DWC\Http\Controllers\DwcHotelsController;
use Modules\DWC\Http\Controllers\DwcBillingCodeController;
use Modules\DWC\Http\Controllers\DwcGuestTypeController;
use Modules\DWC\Http\Controllers\DwcHorseController;
use Modules\DWC\Http\Controllers\DwcReportController;

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
    // Route::resource('dwc', DWCController::class)->names('dwc');
});

Route::group(['middleware' => 'auth', 'prefix' => 'account/dwc'], function () {
    Route::get('guests/import', [DwcGuestsController::class, 'importGuest'])->name('guests.import');
    Route::post('guests/import', [DwcGuestsController::class, 'importStore'])->name('guests.import.store');
    Route::post('guests/import/process', [DwcGuestsController::class, 'importProcess'])->name('guests.import.process');
    // Route::get('import/process/{name}/{id}', [ImportController::class, 'getImportProgress'])->name('import.process.progress');
    Route::resource('guests', DwcGuestsController::class);
    // Route::resource('dwc', DWCController::class)->names('dwc');

    Route::get('arraival-list', [DwcGuestsController::class, 'arraivalList']);
    Route::get('departure-list', [DwcGuestsController::class,'departureList']);

});
