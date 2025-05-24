<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\EvntEventController;
use Modules\Events\Http\Controllers\EventCheckinPointsController;
use Modules\Events\Http\Controllers\EventRegistrationController;
use Modules\Events\Http\Controllers\PublicUrlController;

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

/* EVENTS */
Route::get('/p/events/{slug}', [PublicUrlController::class, 'EventPublicUrlView'])->name('front.event-publicurl.show');
Route::get('/p/{slug}/registration', [PublicUrlController::class, 'EventPublicUrlNewView'])->name('front.event-publicurl-new.show');
Route::get('/p/{slug}/queue_status', [PublicUrlController::class, 'EventQueueStatusView'])->name('front.event-queue-status.show');
Route::get('/get-panchayats/{id}', [PublicUrlController::class, 'getPanchayats'])->name('get_panchayats');
Route::get('/event/qr/download/{slug}/{registration_code}', [PublicUrlController::class, 'EventQrDownload'])->name('front.event.qr.download');
Route::get('/event/new/qr/download/{slug}/{registration_code}', [PublicUrlController::class, 'EventNewQrDownload'])->name('front.event.new.qr.download');
Route::post('/events/sign', [PublicUrlController::class, 'EventRegister'])->name('front.events.register');
Route::post('/events/registration/sign', [PublicUrlController::class, 'NewEventRegister'])->name('front.events.new-register');
Route::get('/event/qr/{slug}/{registration_code}', [PublicUrlController::class, 'qrshow'])->name('event.qr.show');
Route::get('/event/new/qr/{slug}/{registration_code}', [PublicUrlController::class, 'Newqrshow'])->name('event.new.qr.show');
Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    Route::post('event-monthly-on', [EvntEventController::class, 'monthlyOn'])->name('events.monthly_on');
    Route::resource('events', EvntEventController::class);
    Route::post('updateStatus/{id}', [EvntEventController::class, 'updateStatus'])->name('events.update_status');
    Route::get('events/event-status-note/{id}', [EvntEventController::class, 'eventStatusNote'])->name('events.event_status_note');
    //Event Registration
    Route::get('event/event-registration/create/{id}', [EvntEventController::class, 'Registrationcreate'])->name('event.event-registration.create');
    Route::get('event/event-registration/{id}/edit', [EvntEventController::class, 'Registrationedit'])->name('event.event-registration.edit');
    Route::resource('event-registration', EventRegistrationController::class);

    //Event Participant
    Route::get('event/event-participant/create/{id}', [EvntEventController::class, 'Participantcreate'])->name('event.event-participant.create');
    Route::get('event/event-participant/{id}/edit', [EvntEventController::class, 'Participantedit'])->name('event.event-participant.edit');
    Route::post('event/event-participant/store', [EvntEventController::class, 'Participantstore'])->name('event.event-participant.store');
    Route::put('event/event-participant/{id}/update', [EvntEventController::class, 'ParticipantUpdate'])->name('event.event-participant.update');
    Route::delete('event/event-participant/{id}', [EvntEventController::class, 'ParticipantDelete'])->name('event.event-participant.destroy');

    // participation report
    Route::get('event/participation-report', [EventRegistrationController::class, 'participationReport'])->name('event.participation-report');

    // Checked-in participants report
    Route::get('event/checked-in-report', [EventRegistrationController::class, 'checkedInReport'])->name('event.checked-in-report');

    //event-checkin-points
    Route::resource('event-checkin-points', EventCheckinPointsController::class);
});
Route::group([], function () {
    // Route::resource('events', EventsController::class)->names('events');
});
