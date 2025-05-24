<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\Api\EventRegistrationApiController;

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
    Route::get('events', fn(Request $request) => $request->user())->name('events');
});

Route::group([
    'middleware' => 'auth:api2',
    'prefix' => 'app',
], function () {

    Route::group([
        'prefix' => 'event'
    ], function () {
        Route::get('/get-participant-details', [EventRegistrationApiController::class, 'getParticipantDetails']);
        Route::post('/mark-attendance', [EventRegistrationApiController::class, 'markAttendance']);
        Route::get('/{id}/checkin-points', [EventRegistrationApiController::class, 'listCheckinPointsByEvent']);
    });

    // List all events
    Route::get('/events', [EventRegistrationApiController::class, 'listAllEvents']);
});

// Route::post('/app/save-queue-status', function (Request $request) {
//     try {
//         // Validate the request
//         $validated = $request->validate([
//             'event_id' => 'required|integer|exists:evnt_events,id',
//             'gents' => 'nullable|string|max:255',
//             'ladies' => 'nullable|string|max:255',
//             'token' => 'required|string',
//         ]);

//         // Verify token (simple security check - should be replaced with more robust solution)
//         if ($validated['token'] !== '7fG2hJ9kLpQ1') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Unauthorized access'
//             ], 401);
//         }

//         // Find existing record or create new one
//         if($request->type == 'gents'){
//             $queueStatus = \Modules\Events\Entities\EventQueueStatus::updateOrCreate(
//                 ['event_id' => $validated['event_id']],
//                 [
//                     'gents' => $validated['gents'] ?? ''
//                 ]
//             );
//         }
//         if($request->type == 'ladies'){
//             $queueStatus = \Modules\Events\Entities\EventQueueStatus::updateOrCreate(
//                 ['event_id' => $validated['event_id']],
//                 [
//                     'ladies' => $validated['ladies'] ?? '',
//                 ]
//             );
//         }


//         return response()->json([
//             'status' => 'success',
//             'message' => 'Queue status updated successfully',
//             'data' => $queueStatus
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'An error occurred while updating queue status',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// });

// // Add this route in the same group as save-queue-status
// Route::get('/app/get-queue-status/{event_id}', function ($event_id) {
//     try {
//         $queueStatus = \Modules\Events\Entities\EventQueueStatus::where('event_id', $event_id)->first();

//         if (!$queueStatus) {
//             return response()->json([
//                 'status' => 'success',
//                 'data' => [
//                     'event_id' => $event_id,
//                     'gents' => '', // Default values
//                     'ladies' => '' // Default values
//                 ]
//             ]);
//         }

//         return response()->json([
//             'status' => 'success',
//             'data' => $queueStatus
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'An error occurred while fetching queue status',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// });


// Queue Status API Routes
Route::post('app/save-queue-status', function (Request $request) {
    try {
        // Validate the request
        $validated = $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'gents' => 'nullable|string|max:255',
            'ladies' => 'nullable|string|max:255',
            'token' => 'required|string',
            'countdown_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        // Verify token (simple security check - should be replaced with more robust solution)
        if ($validated['token'] !== '7fG2hJ9kLpQ1') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        // If we're updating gents queue, automatically reset countdown to 20 minutes
        $resetCountdown = false;
        if ($request->type == 'gents' && isset($validated['gents'])) {
            $resetCountdown = true;
        }

        // Use provided countdown_minutes or default to 20 when gents updated
        $countdownMinutes = isset($validated['countdown_minutes'])
            ? $validated['countdown_minutes']
            : ($resetCountdown ? 20 : null);

        // Handle countdown minutes if it needs updating
        if ($countdownMinutes !== null) {
            \Modules\Events\Entities\EventQueueStatus::updateOrCreate(
                ['event_id' => $validated['event_id']],
                ['countdown_minutes' => $countdownMinutes]
            );
        }

        // Handle gents or ladies updates based on type
        if ($request->type == 'gents') {
            $queueStatus = \Modules\Events\Entities\EventQueueStatus::updateOrCreate(
                ['event_id' => $validated['event_id']],
                [
                    'gents' => $validated['gents'] ?? ''
                ]
            );
        }
        if ($request->type == 'ladies') {
            $queueStatus = \Modules\Events\Entities\EventQueueStatus::updateOrCreate(
                ['event_id' => $validated['event_id']],
                [
                    'ladies' => $validated['ladies'] ?? '',
                ]
            );
        }

        // Get the updated queue status for response
        $queueStatus = \Modules\Events\Entities\EventQueueStatus::where('event_id', $validated['event_id'])->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Queue status updated successfully',
            'data' => $queueStatus
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating queue status',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Get queue status route
Route::get('app/get-queue-status/{event_id}', function ($event_id) {
    try {
        $queueStatus = \Modules\Events\Entities\EventQueueStatus::where('event_id', $event_id)->first();

        if (!$queueStatus) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'event_id' => $event_id,
                    'gents' => '', // Default values
                    'ladies' => '', // Default values
                    'countdown_minutes' => 20 // Default countdown time
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $queueStatus
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while fetching queue status',
            'error' => $e->getMessage()
        ], 500);
    }
});
