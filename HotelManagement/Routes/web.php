<?php

use Illuminate\Support\Facades\Route;
use Modules\HotelManagement\Http\Controllers\HmBookingSettingController;
use Modules\HotelManagement\Http\Controllers\HmBookingTypeSettingController;
use Modules\HotelManagement\Http\Controllers\HotelManagementController;
use Modules\HotelManagement\Http\Controllers\HotelManagementSettingController;
use Modules\HotelManagement\Http\Controllers\HotelManagementFloorSettingController;
use Modules\HotelManagement\Http\Controllers\HotelManagementRoomtypesSettingController;
use Modules\HotelManagement\Http\Controllers\HmFacilitiesSettingController;
use Modules\HotelManagement\Http\Controllers\HmServiceSettingController;
use Modules\HotelManagement\Http\Controllers\HotelManagementPropertiesController;
use Modules\HotelManagement\Http\Controllers\HMGuestController;
use Modules\HotelManagement\Http\Controllers\HMRoomsController;
use Modules\HotelManagement\Http\Controllers\PropertyFileController;
use Modules\HotelManagement\Http\Controllers\HMRoomsFileController;
use Modules\HotelManagement\Http\Controllers\HMCheckinController;

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
//     Route::resource('hotelmanagement', HotelManagementController::class)->names('hotelmanagement');
// });

// Admin routes
Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {


    //HotelManagementSetting
    Route::resource('hotelmanagement-settings', HotelManagementSettingController::class);
    //properties
    Route::resource('hm-properties', HotelManagementPropertiesController::class);
    //properties files
    Route::post('propertyfiles/store-link', [PropertyFileController::class, 'storeLink'])->name('propertyfiles.store_link');
    Route::get('propertyfiles/download/{id}', [PropertyFileController::class, 'download'])->name('propertyfiles.download');
    Route::get('propertyfiles/thumbnail', [PropertyFileController::class, 'thumbnailShow'])->name('propertyfiles.thumbnail');
    Route::post('propertyfiles/multiple-upload', [PropertyFileController::class, 'storeMultiple'])->name('propertyfiles.multiple_upload');
    Route::resource('propertyfiles', PropertyFileController::class);
    //HM Rooms
    Route::resource('hm-rooms', HMRoomsController::class);
    //HM Rooms files
    Route::post('hmroomfiles/store-link', [HMRoomsFileController::class, 'storeLink'])->name('hmroomfiles.store_link');
    Route::get('hmroomfiles/download/{id}', [HMRoomsFileController::class, 'download'])->name('hmroomfiles.download');
    Route::get('hmroomfiles/thumbnail', [HMRoomsFileController::class, 'thumbnailShow'])->name('hmroomfiles.thumbnail');
    Route::post('hmroomfiles/multiple-upload', [HMRoomsFileController::class, 'storeMultiple'])->name('hmroomfiles.multiple_upload');
    Route::resource('hmroomfiles', HMRoomsFileController::class);
    //HmGuests
    Route::resource('hm-guests', HMGuestController::class);
    //Hm Booking Reservation
    Route::get('get-room-types', [HMCheckinController::class, 'getRoomTypes'])->name('rooms.types');
    Route::get('get-room-numbers', [HMCheckinController::class, 'getRoomNumbers'])->name('rooms.numbers');
    //   Route::get('hm-checkin/guests', [HMCheckinController::class, 'fetchGuests'])->name('hmcheckin.guests');
    // Route::get('/api/guests', [HMCheckinController::class, 'fetchGuests'])->name('hmcheckin.api.guests');
    Route::resource('hm-checkin', HMCheckinController::class);


    //HotelManagementSetting floor
    Route::resource('hotelmanagement-floor-settings', HotelManagementFloorSettingController::class);
    Route::post('/hotelmanagement/floor/update-status', [HotelManagementFloorSettingController::class, 'updateStatus'])->name('hotelmanagement.floor.update-status');

    //HotelManagementSetting Room Type
    Route::resource('hm-roomtypes-settings', HotelManagementRoomtypesSettingController::class);
    Route::post('/hotelmanagement/roomtypes/update-status', [HotelManagementRoomtypesSettingController::class, 'updateStatus'])->name('hotelmanagement.roomtypes.update-status');

    //HotelManagementSetting facilities
    Route::resource('hm-facilities-settings', HmFacilitiesSettingController::class);
    Route::post('/hotelmanagement/facilities/update-status', [HmFacilitiesSettingController::class, 'updateStatus'])->name('hotelmanagement.facilities.update-status');
    //HotelManagementSetting Service
    Route::resource('hm-services-settings', HmServiceSettingController::class);
    Route::post('/hotelmanagement/services/update-status', [HmServiceSettingController::class, 'updateStatus'])->name('hotelmanagement.services.update-status');
    //HotelManagementSetting hmbookingsource
    Route::resource('hmbookingsource-settings', HmBookingSettingController::class);
    Route::post('/hmbookingsource-settings/update-status', [HmBookingSettingController::class, 'updateStatus'])->name('hmbookingsource-settings.update-status');
    //HotelManagementSetting hmbookingsource
    Route::resource('hmbookingtype-settings', HmBookingTypeSettingController::class);
    Route::post('/hmbookingtype-settings/update-status', [HmBookingTypeSettingController::class, 'updateStatus'])->name('hmbookingtype-settings.update-status');

    //Settings Routes
    Route::group(['prefix' => 'settings'], function () {

    });
    //     Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    //         //properties



    // });

    Route::group(['prefix' => 'hms'], function () {
        Route::get('/', function () {
            return "HMS Module";
        });
    });


});
