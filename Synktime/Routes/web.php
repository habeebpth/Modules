<?php

use Illuminate\Support\Facades\Route;
use Modules\Synktime\Http\Controllers\EntitySyncController;
use Modules\Synktime\Http\Controllers\SynkingController;
use Modules\Synktime\Http\Controllers\SynktimeSettingController;

// use $MODULE_NAMESPACE$\Synktime\$CONTROLLER_NAMESPACE$\SynktimeController;

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

Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    //synktime-settings
    Route::post('synktime-settings/configuration/update/{id?}', [SynktimeSettingController::class, 'updateConfiguration'])
        ->name('synktime-settings.updateconfiguration');
    Route::resource('synktime-settings', SynktimeSettingController::class);
    //Synking History
    Route::resource('synking-history', SynkingController::class);

    // Entity Syncing Routes
    Route::get('entity-sync', [EntitySyncController::class, 'showSyncOptions'])
        ->name('entity-sync.options');
    Route::post('entity-sync/departments', [EntitySyncController::class, 'syncDepartments'])
        ->name('entity-sync.departments');
    Route::post('entity-sync/areas', [EntitySyncController::class, 'syncAreas'])
        ->name('entity-sync.areas');
    Route::post('entity-sync/employees', [EntitySyncController::class, 'syncEmployees'])
        ->name('entity-sync.employees');
    Route::post('entity-sync/attendance', [EntitySyncController::class, 'syncAttendance'])->name('entity-sync.attendance');
    Route::get('attendance-dashboard', [EntitySyncController::class, 'attendanceDashboard'])->name('entity-sync.attendance-dashboard');
});
Route::group([], function () {
    Route::resource('synktime', SynktimeController::class)->names('synktime');
});
