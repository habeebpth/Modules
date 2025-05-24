<?php

use Illuminate\Support\Facades\Route;
// use $MODULE_NAMESPACE$\Reward\$CONTROLLER_NAMESPACE$\RewardController;
use Modules\Reward\Http\Controllers\RewardCustomerController;
use Modules\Reward\Http\Controllers\RewardTransactionController;

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

// Route::group([], function () {
//     Route::resource('reward', RewardController::class)->names('reward');
// });
Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    Route::resource('reward-customers', RewardCustomerController::class);

    Route::resource('reward-transactions', RewardTransactionController::class);
});
