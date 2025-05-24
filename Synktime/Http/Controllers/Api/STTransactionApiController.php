<?php

namespace Modules\Synktime\Http\Controllers\Api;

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

class STTransactionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('synktime::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('synktime::create');
    }

    /**
     * Store a newly created resource in storage.
     */


     public function store(Request $request)
     {
         try {
             // Validate input data
             $validatedData = $request->validate([
                 'punch_state'      => 'required|string',
                 'latitude'         => 'nullable|numeric',
                 'longitude'        => 'nullable|numeric',
                 'gps_location'     => 'nullable|string',
                 'attendance_type'  => 'nullable|string',
                 'employee_id'      => 'nullable|integer',
                 'device_id'        => 'nullable|string',
                 'device_name'      => 'nullable|string',
                 'type'             => 'nullable|string',
             ]);

             // Get current user and time
             $user = User::findOrFail(auth()->id());
             $nowDateTime = now($this->company->timezone);
  // Use Carbon's now() for current datetime
             $nowTime = now($this->company->timezone)->format('H:i');
             // Get current time in 'H:i' format
             // Get approval type from employee details
             $employeeDetails = EmployeeDetails::where('user_id', $user->id)->first();
             $approveType = $employeeDetails->approve_type ?? 'auto';

             // Save STTransaction
             $transaction = new STTransaction();
             $transaction->company_id = $user->company_id;
             $transaction->user_id = $user->id;
             $transaction->employee_id = $validatedData['employee_id'] ?? null;
             $transaction->employee_name = $user->name;
             $transaction->punch_state = $validatedData['punch_state'];
             $stateMap = [
                 '0' => 'Check-In',
                 '1' => 'Check-Out',
                 '2' => 'Break-Out',
                 '3' => 'Break-In',
             ];
             $transaction->punch_state_display = $stateMap[$validatedData['punch_state']] ?? null;
             $transaction->latitude = $validatedData['latitude'] ?? null;
             $transaction->longitude = $validatedData['longitude'] ?? null;
             $transaction->gps_location = $validatedData['gps_location'] ?? null;
             $transaction->attendance_type = $validatedData['type'] ?? $validatedData['attendance_type'] ?? null;
             $transaction->punch_time = $nowDateTime;
             $transaction->device_id = $validatedData['device_id'] ?? null;
             $transaction->device_name = $validatedData['device_name'] ?? null;
             if ($approveType === 'manual') {
                 $transaction->approved = 'n';  // Mark as 'not approved' for manual approval
             }
             $transaction->save();

             // Attendance logic
             if ($validatedData['punch_state'] === '0') { // Check-In
                 $attendanceSettings = AttendanceSetting::where('company_id', $user->company_id)->first();

                 $late = !empty($attendanceSettings->office_start_time)
                     ? (strtotime($nowTime) > (strtotime($attendanceSettings->office_start_time) + strtotime($attendanceSettings->late_mark_duration . ':00')) ? 1 : 0)
                     : 'no';

                 $halfDay = !empty($attendanceSettings->halfday_mark_time)
                     ? (strtotime($nowTime) > strtotime($attendanceSettings->halfday_mark_time) ? 1 : 0)
                     : 'no';

                 if ($approveType === 'manual') {
                     $existingRequest = AttendanceRequest::where('user_id', $user->id)
                         ->whereDate('clock_in_time', $nowDateTime->toDateString()) // Correct date comparison using Carbon
                         ->first();

                     if (!$existingRequest) {
                         AttendanceRequest::create([
                             'company_id'          => $user->company_id,
                             'user_id'             => $user->id,
                             'location_id'         => null,
                             'clock_in_time'       => $nowDateTime,
                             'auto_clock_out'      => 0,
                             'clock_in_ip'         => $request->ip(),
                             'working_from'        => 'office',
                             'half_day'            => $halfDay ? 'yes' : 'no',
                             'half_day_type'       => null,
                             'added_by'            => $user->id,
                             'last_updated_by'     => $user->id,
                             'latitude'            => $validatedData['latitude'] ?? null,
                             'longitude'           => $validatedData['longitude'] ?? null,
                             'employee_shift_id'   => null,
                             'work_from_type'      => 'office',
                             'overwrite_attendance' => 'no',
                             'approved'            => 'n',
                             'attendance_type'     => $validatedData['type'] ?? $validatedData['attendance_type'] ?? null,
                             'location_coordinates' => $validatedData['gps_location'] ?? null,
                         ]);
                     }
                 } else {
                     Attendance::updateOrCreate(
                         [
                             'user_id' => $user->id,
                             'late' => $late,
                             'half_day' => $halfDay,
                             'work_from_type' => 'office',
                             'overwrite_attendance' => 'no',
                         ],
                         [
                             'clock_in_time' => $nowDateTime,
                             'auto_clock_out' => 0,
                             'clock_in_ip' => $request->ip(),
                             'latitude' => $validatedData['latitude'] ?? null,
                             'longitude' => $validatedData['longitude'] ?? null,
                         ]
                     );
                 }
             }

             if ($validatedData['punch_state'] === '1') { // Check-Out
                 if ($approveType === 'manual') {
                     $existingRequest = AttendanceRequest::where('user_id', $user->id)
                         ->whereDate('clock_in_time', $nowDateTime->toDateString()) // Correct date comparison using Carbon
                         ->first();
                     if ($existingRequest) {
                         $existingRequest->update([
                             'clock_out_time'   => $nowDateTime,
                             'clock_out_ip'     => $request->ip(),
                             'last_updated_by'  => $user->id,
                             'updated_at'       => now(),
                         ]);
                     }
                 } else {
                     $attendance = Attendance::where('user_id', $user->id)
                         ->whereDate('clock_in_time', $nowDateTime->toDateString()) // Correct date comparison using Carbon
                         ->latest('clock_in_time')
                         ->first();

                     if (!$attendance || $attendance->clock_out_time) {
                         Attendance::updateOrCreate(
                             [
                                 'user_id' => $user->id,
                                 'late' => 'no',
                                 'half_day' => 'no',
                                 'work_from_type' => 'office',
                                 'overwrite_attendance' => 'no',
                             ],
                             [
                                 'clock_out_time' => $nowDateTime,
                                 'auto_clock_out' => 0,
                                 'clock_in_ip' => $request->ip(),
                                 'latitude' => $validatedData['latitude'] ?? null,
                                 'longitude' => $validatedData['longitude'] ?? null,
                             ]
                         );
                     } else {
                         $attendance->clock_out_time = $nowDateTime;
                         $attendance->clock_out_ip = $request->ip();
                         $attendance->save();
                     }
                 }
             }

             return response()->json([
                 'error' => false,
                 'message' => 'Transaction and attendance recorded successfully',
                 'data' => $transaction,
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error' => true,
                 'message' => 'Failed to store transaction: ' . $e->getMessage(),
                 'trace_back' => $e->getTraceAsString(),
             ], 500);
         }
     }




//  public function getPunchStatus()
// {
//     try {
//         $today = now()->toDateString();

//         $punch = STTransaction::where('user_id', auth()->id())
//             ->whereDate('punch_time', $today)
//             ->orderByDesc('punch_time')
//             ->first();

//         return response()->json([
//             'error' => false,
//             'status' => $punch?->punch_state ?? null,
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error'      => true,
//             'message'    => 'Failed to get punch status: ' . $e->getMessage(),
//             'trace_back' => $e->getTraceAsString(),
//         ], 500);
//     }
// }

public function getPunchStatus()
{
    try {
        $today = now()->toDateString();

        $punches = STTransaction::where('user_id', auth()->id())
            ->whereDate('punch_time', $today)
            ->orderBy('punch_time')
            ->get();

        $firstCheckIn = null;
        $lastCheckOut = null;
        $totalWorkedSeconds = 0;

        for ($i = 0; $i < $punches->count(); $i++) {
            $current = $punches[$i];

            if ($current->punch_state == 0) {
                // First check-in
                if (!$firstCheckIn) {
                    $firstCheckIn = $current->punch_time;
                }

                // If next is a checkout, calculate worked time
                if (isset($punches[$i + 1]) && $punches[$i + 1]->punch_state == 1) {
                    $checkOut = $punches[$i + 1]->punch_time;
                    $lastCheckOut = $checkOut;
                    $workedSeconds = strtotime($checkOut) - strtotime($current->punch_time);
                    $totalWorkedSeconds += max(0, $workedSeconds);
                    $i++; // skip processed checkout
                }
            }
        }

        $lastPunchState = optional($punches->last())->punch_state;

        return response()->json([
            'error' => false,
            'first_checkin' => $firstCheckIn ? \Carbon\Carbon::parse($firstCheckIn)->format('H:i:s') : null,
            'last_checkout' => $lastCheckOut ? \Carbon\Carbon::parse($lastCheckOut)->format('H:i:s') : null,
            'worked_hours' => gmdate('H:i:s', $totalWorkedSeconds),
            'status' => $lastPunchState, // 0 = check-in, 1 = check-out
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Failed to get punch status: ' . $e->getMessage(),
            'trace_back' => $e->getTraceAsString(),
        ], 500);
    }
}



    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('synktime::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('synktime::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
