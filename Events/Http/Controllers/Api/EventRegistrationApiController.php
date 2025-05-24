<?php

namespace Modules\Events\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Project;
use Modules\Synktime\Entities\STTransaction;
use App\Models\User;
use App\Models\AttendanceRequest;
use App\Models\EmployeeDetails;
use Carbon\Carbon;
use App\Models\AttendanceSetting;
use App\Models\Attendance;
use Modules\Events\Entities\EventRegistration;
use Modules\Events\Entities\EventParticipant;
use Modules\Events\Entities\EvntEvent;
use Modules\Events\Entities\EventCheckinPoint;

class EventRegistrationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getParticipantDetails(Request $request)
    {
        try {
            $request->validate([
                'registration_code' => 'required|string|exists:event_registrations,registration_code',
            ]);

            $participant = EventRegistration::with('event')->where('registration_code', $request->registration_code)->first();

            if (!$participant) {
                return response()->json([
                    'error' => true,
                    'message' => 'Participant not found',
                ], 404);
            }

            // Get all check-in records related to this participant
            $eventParticipants = EventParticipant::with('checkinpoint')
                ->where('event_registration_id', $participant->id)
                ->get();

            // Format the check-in point details
            $checkinPoints = $eventParticipants->map(function ($entry) {
                return [
                    'name' => $entry->checkinpoint->name ?? 'N/A',
                    'checked_in_at' => $entry->created_at->toDateTimeString()
                ];
            });

            $allowedSeats = ($participant->allotted_seats_start && $participant->allotted_seats_end)
                ? implode(', ', range($participant->allotted_seats_start, $participant->allotted_seats_end))
                : 'N/A';

            return response()->json([
                'error' => false,
                'message' => 'Participant details retrieved successfully',
                'data' => [
                    'id' => $participant->id,
                    'student_id' => $participant->student_id ?? '',
                    'name' => $participant->name,
                    'mobile' => $participant->mobile,
                    'event_name' => $participant->event->name ?? 'N/A',
                    'registration_code' => $participant->registration_code,
                    'allowed_seats' => $allowedSeats,
                    'no_of_participants' => $participant->no_of_participants,
                    'allotted_seats_start' => $participant->allotted_seats_start,
                    'allotted_seats_end' => $participant->allotted_seats_end,
                    'checkin_points' => $checkinPoints,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to retrieve participant details.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAttendance(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'registration_code' => 'required|exists:event_registrations,registration_code',
                'checkin_point_id' => 'nullable|exists:event_checkin_points,id',
                'event_id' => 'required|exists:evnt_events,id',
                'no_of_participants' => 'nullable|integer|min:1',
                'no_of_seats_filled_start' => 'nullable|integer|min:1',
                'no_of_seats_filled_end' => 'nullable|integer|gte:no_of_seats_filled_start',
                'remarks' => 'nullable|string',
            ]);

            // Fetch event registration
            $eventRegistration = EventRegistration::where('registration_code', $validatedData['registration_code'])->first();

            if (!$eventRegistration) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid registration code.',
                ], 404);
            }

            // Check if seat numbers overlap with existing participants
            // if (!empty($validatedData['no_of_seats_filled_start']) && !empty($validatedData['no_of_seats_filled_end'])) {
            //     $overlap = EventParticipant::where('event_id', $validatedData['event_id'])
            //         ->where(function ($query) use ($validatedData) {
            //             $query->where('no_of_seats_filled_start', '<=', $validatedData['no_of_seats_filled_end'])
            //                 ->where('no_of_seats_filled_end', '>=', $validatedData['no_of_seats_filled_start']);
            //         })
            //         ->exists();

            //     if ($overlap) {
            //         return response()->json([
            //             'error' => true,
            //             'message' => 'Some seats are already occupied.',
            //         ], 400);
            //     }
            // }

            // Store data
            $eventParticipant = new EventParticipant();
            $eventParticipant->company_id = $eventRegistration->company_id;
            $eventParticipant->event_registration_id = $eventRegistration->id;
            $eventParticipant->checkin_point_id = $validatedData['checkin_point_id'] ?? null;
            $eventParticipant->checkin_time = now();
            $eventParticipant->no_of_participants = $validatedData['no_of_participants'] ?? 1;
            $eventParticipant->no_of_seats_filled_start = $validatedData['no_of_seats_filled_start'] ?? null;
            $eventParticipant->no_of_seats_filled_end = $validatedData['no_of_seats_filled_end'] ?? null;
            $eventParticipant->remarks = $validatedData['remarks'] ?? null;
            $eventParticipant->event_id = $validatedData['event_id'];
            $eventParticipant->added_by =  auth('api2')->user()->user->id;
            $eventParticipant->save();

            if ($eventParticipant->checkin_point_id == 2) {
                $data = [
                    'mobile'            => $eventRegistration->country_wtsap_phonecode . $eventRegistration->whatsapp,
                    'phoneNumberId'     => '379394621917932',
                    'templateName'      => 'solution_feebck',
                    'documentFilename'  => 'Solution QR',
                    'reg_code' => $eventRegistration->registration_code,
                    'accessToken'       => 'EAAOQ9RUIZB9EBO5dcTXR4IN64RsKnuifIRDG6g90ky2ccjdvmZCFZCE89bdMoQoYJT1hTXZCerCzeLlpMP9gA7sGZCW50KO1lEnELSIFYNlzKq4Xbo5w98zrX6zTeBjtGZA8ZBC8EaR0Tg00yPRTxXdtKF0bQuFfHWNjofAMW0d6CRZCRHve5AIiviEiKygsBMGyeAZDZD',
                ];

                // if($eventRegistration->whatsapp == '9746937888')
                // {
                $this->sendMessageNew($data);
                // }


            }

            return response()->json([
                'error' => false,
                'message' => 'Attendance marked successfully.',
                'data' => $eventParticipant,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to mark attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendMessageNew($data, $mediaId = null)
    {
        $accessToken = $data['accessToken'];
        $phoneNumberId = $data['phoneNumberId'];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$phoneNumberId/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ]);

        // Define the POST data
        $postData = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $data['mobile'],
            'type' => 'template',
            'template' => [
                'name' => $data['templateName'],
                'language' => [
                    'code' => 'ml'
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['reg_code']
                            ],
                        ]
                    ]
                ]
            ]
        ];

        // Convert POST data to JSON
        $jsonPostData = json_encode($postData);

        // Set the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Check for errors and return response
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error
            ];
        } else {
            $responseData = json_decode($response, true);
            curl_close($ch);
            return [
                'success' => true,
                'data' => $responseData
            ];
        }
    }

    public function listAllEvents()
    {
        try {
            $events = EvntEvent::all();

            return response()->json([
                'error' => false,
                'message' => 'Events retrieved successfully.',
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to retrieve events: ' . $e->getMessage(),
                'trace_back' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function listCheckinPointsByEvent($id)
    {
        try {
            // Validate if the id exists in the event_checkin_points table
            if (!EventCheckinPoint::where('event_id', $id)->exists()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid event_id or no check-in points found for the given event.',
                ], 404);
            }

            $checkinPoints = EventCheckinPoint::where('event_id', $id)->get();

            return response()->json([
                'error' => false,
                'message' => 'Check-in points retrieved successfully.',
                'data' => $checkinPoints,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to retrieve check-in points: ' . $e->getMessage(),
                'trace_back' => $e->getTraceAsString(),
            ], 500);
        }
    }


    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
