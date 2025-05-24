<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccCategoriesSettingController;
use Modules\Accounting\Http\Controllers\AccountingSettingController;
use Modules\Accounting\Http\Controllers\AccountsController;
use Modules\Accounting\Http\Controllers\AccTypesSettingController;
use Modules\Accounting\Http\Controllers\JournalEntriesController;

// use $MODULE_NAMESPACE$\Accounting\$CONTROLLER_NAMESPACE$\AccountingController;

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

    //accounting-settings
    Route::resource('accounting-settings', AccountingSettingController::class);
    //finance settings
    Route::post('finance-settings/update/{id}', [AccountingSettingController::class, 'updateFinanceSetting'])->name('finance_settings.update');


    // accounttypes
    Route::resource('acc-types-settings', AccTypesSettingController::class);
    Route::post('/acc-types-settings/update-status', [AccTypesSettingController::class, 'updateStatus'])->name('acc-types-settings.update-status');

    // accountcategories
    Route::resource('acc-categories-settings', AccCategoriesSettingController::class);
    Route::post('/acc-categories-settings/update-status', [AccCategoriesSettingController::class, 'updateStatus'])->name('acc-categories-settings.update-status');

    //accounts
    Route::get('accounts/accounts-hierarchy', [AccountsController::class, 'hierarchyData'])->name('accounts.hierarchy');
    Route::get('/accounts/check-children/{id}', [AccountsController::class, 'checkChildren'])->name('accounts.checkChildren');
    Route::delete('accounts/destroyMultiple', [AccountsController::class, 'destroyMultiple'])->name('accounts.destroyMultiple');


    Route::resource('accounts', AccountsController::class);

    Route::get('journal-entries', [JournalEntriesController::class, 'index'])->name('journal-entries.index');
    Route::get('journal-entries/create', [JournalEntriesController::class, 'create'])->name('journal-entries.create');
    Route::post('journal-entries', [JournalEntriesController::class, 'store'])->name('journal-entries.store');
    Route::get('journal-entries/{id}', [JournalEntriesController::class, 'show'])->name('journal-entries.show');
    Route::get('journal-entries/{id}/edit', [JournalEntriesController::class, 'edit'])->name('journal-entries.edit');
    Route::put('journal-entries/{id}', [JournalEntriesController::class, 'update'])->name('journal-entries.update');
    Route::delete('journal-entries/{id}', [JournalEntriesController::class, 'destroy'])->name('journal-entries.destroy');
    Route::post('journal-entries/post', [JournalEntriesController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/void', [JournalEntriesController::class, 'void'])->name('journal-entries.void');
});
Route::group([], function () {
    Route::resource('accounting', AccountingController::class)->names('accounting');
});
