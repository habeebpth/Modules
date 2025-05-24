<?php

namespace Modules\Synktime\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\Synktime\Entities\SynkingHistory;
use Exception;

class AttendanceSyncService
{
    /**
     * Sync attendance data from API
     *
     * @param array $attendanceData
     * @param int $companyId
     * @param int $userId
     * @return int Number of attendance records synced
     */
    public function syncAttendance(array $attendanceData, int $companyId, int $userId): int
    {
        $syncCount = 0;

        try {
            foreach ($attendanceData as $employee) {
                // Skip employees with empty names
                if (empty($employee['name'])) {
                    Log::info("Skipping employee with empty name, ID: " . $employee['employee_id']);
                    continue;
                }

                // Find the user based on employee_id
                $employeeDetail = EmployeeDetails::where('employee_id', $employee['employee_id'])->first();

                if (!$employeeDetail) {
                    Log::info("Employee not found with ID: " . $employee['employee_id']);
                    continue;
                }

                $user = User::find($employeeDetail->user_id);

                if (!$user) {
                    Log::info("User not found for employee ID: " . $employee['employee_id']);
                    continue;
                }

                // Process the attendance summary
                if (isset($employee['summary']) && is_array($employee['summary'])) {
                    foreach ($employee['summary'] as $attendance) {
                        // Skip weekends or days without proper attendance data
                        if ($attendance['attendance'] === 'Weekend' ||
                            ($attendance['attendance'] === 'Absent' && empty($attendance['checkin']))) {
                            continue;
                        }

                        // Only process records with a check-in time
                        if (!empty($attendance['checkin'])) {
                            try {
                                // Create the full datetime strings for check-in and check-out
                                $dateStr = $attendance['date'];
                                $checkInTimeStr = $attendance['checkin'];

                                // Only proceed if we have valid check-in data
                                if (empty($checkInTimeStr)) {
                                    continue;
                                }

                                // Parse the date and time
                                $clockInTime = Carbon::parse($dateStr . ' ' . $checkInTimeStr);

                                // Handle checkout if available
                                $clockOutTime = null;
                                if (!empty($attendance['checkout'])) {
                                    $checkOutTimeStr = $attendance['checkout'];
                                    $clockOutTime = Carbon::parse($dateStr . ' ' . $checkOutTimeStr);
                                }

                                // Check if attendance record already exists for this date and user
                                $existingAttendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('clock_in_time', $clockInTime->toDateString())
                                    ->first();

                                if ($existingAttendance) {
                                    // Update existing record
                                    $existingAttendance->clock_in_time = $clockInTime;
                                    if ($clockOutTime) {
                                        $existingAttendance->clock_out_time = $clockOutTime;
                                    }

                                    // Handle late status
                                    if (!empty($attendance['total_late_minutes']) && $attendance['total_late_minutes'] != '0') {
                                        $existingAttendance->late = 'yes';
                                    }

                                    $existingAttendance->last_updated_by = $userId;
                                    $existingAttendance->updated_at = now();
                                    $existingAttendance->save();

                                    Log::info("Updated attendance for user ID: {$user->id}, date: {$dateStr}");
                                } else {
                                    // Create new attendance record
                                    $attendanceRecord = new Attendance();
                                    $attendanceRecord->company_id = $companyId;
                                    $attendanceRecord->user_id = $user->id;
                                    $attendanceRecord->clock_in_time = $clockInTime;
                                    if ($clockOutTime) {
                                        $attendanceRecord->clock_out_time = $clockOutTime;
                                    }

                                    // Handle late status based on late minutes
                                    if (!empty($attendance['total_late_minutes']) && $attendance['total_late_minutes'] != '0') {
                                        $attendanceRecord->late = 'yes';
                                    } else {
                                        $attendanceRecord->late = 'no';
                                    }

                                    // Set default values
                                    $attendanceRecord->clock_in_ip = '127.0.0.1'; // Default IP
                                    $attendanceRecord->working_from = 'office';
                                    $attendanceRecord->work_from_type = 'office';
                                    $attendanceRecord->half_day = 'no';
                                    $attendanceRecord->added_by = $userId;
                                    $attendanceRecord->created_at = now();
                                    $attendanceRecord->updated_at = now();
                                    $attendanceRecord->save();

                                    Log::info("Created attendance for user ID: {$user->id}, date: {$dateStr}");
                                }

                                $syncCount++;
                            } catch (Exception $ex) {
                                Log::error("Error processing attendance for employee {$employee['name']}: " . $ex->getMessage());
                                Log::error($ex->getTraceAsString());
                            }
                        }
                    }
                }
            }

            // Record sync history
            $this->recordSyncHistory($companyId, $userId, $syncCount);

            return $syncCount;
        } catch (Exception $e) {
            Log::error("Error in syncAttendance: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Record sync history
     *
     * @param int $companyId
     * @param int $userId
     * @param int $syncCount
     * @return SynkingHistory
     */
    private function recordSyncHistory(int $companyId, int $userId, int $syncCount): SynkingHistory
    {
        $history = SynkingHistory::create([
            'company_id' => $companyId,
            'created_by' => $userId,
            'updated_by' => $userId,
            'sync_type' => 'attendance',
            'total_synced' => $syncCount,
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);

        return $history;
    }
}
