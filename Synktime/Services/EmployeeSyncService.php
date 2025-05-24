<?php

namespace Modules\Synktime\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\EmployeeDetails;
use App\Models\Role;
use App\Models\UserAuth;
use App\Models\CompanyAddress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Modules\Synktime\Entities\SynkingHistory;

class EmployeeSyncService
{
    /**
     * Sync employees from API data
     *
     * @param array $employees
     * @param int $companyId
     * @param int $userId
     * @return int Number of employees synced
     */
    public function syncEmployees(array $employees, int $companyId, int $userId): int
    {
        $syncCount = 0;

        try {
            // Get employee role
            $employeeRole = Role::where('name', 'employee')->first();

            foreach ($employees as $employee) {
                // Log the incoming employee data
                Log::info('Processing employee: ' . json_encode($employee));

                // Check if employee exists by employee_id
                $existingEmployee = EmployeeDetails::where('employee_id', $employee['employee_id'])->first();

                if (!$existingEmployee) {
                    // Get department ID if exists
                    $departmentId = null;
                    if (!empty($employee['department_id'])) {
                        $department = Team::where('team_name', $employee['department_name'])->first();
                        if ($department) {
                            $departmentId = $department->id;
                        }
                    }

                    try {
                        // Generate the email
                        $email = $employee['email'] ?? $employee['employee_id'] . '@example.com';

                        // Check if a user with this email already exists for this company
                        $existingUser = User::where('email', $email)
                            ->where('company_id', $companyId)
                            ->first();

                        if ($existingUser) {
                            Log::info('User with email already exists for this company: ' . $email);

                            // Either skip this employee or update the existing user
                            // For now, we'll skip
                            continue;
                        }

                        // First check/create user_auth entry
                        $userAuth = UserAuth::where('email', $email)->first();

                        if (!$userAuth) {
                            $userAuth = new UserAuth();
                            $userAuth->email = $email;
                            $userAuth->password = Hash::make(Str::random(10)); // Generate random password
                            $userAuth->save();

                            Log::info('Created user_auth with ID: ' . $userAuth->id);
                        } else {
                            Log::info('Using existing user_auth with ID: ' . $userAuth->id);
                        }

                        // Create User with a unique email for this company
                        // One approach: generate a unique email by appending a random string if needed
                        $userEmail = $email;
                        $attempts = 0;

                        while (User::where('email', $userEmail)->where('company_id', $companyId)->exists() && $attempts < 5) {
                            $userEmail = substr($email, 0, strpos($email, '@')) . '+' . Str::random(5) . '@' . substr($email, strpos($email, '@') + 1);
                            $attempts++;
                        }

                        $user = new User();
                        $user->name = $employee['first_name'] . ' ' . $employee['last_name'];
                        $user->email = $userEmail;
                        $user->user_auth_id = $userAuth->id; // Link to user_auth record
                        $user->company_id = $companyId;

                        // Log before save
                        Log::info('About to save user: ' . json_encode($user->toArray()));

                        $user->save();

                        // Log the saved user to verify it was created correctly
                        Log::info('User saved with ID: ' . $user->id);

                        // Attach role
                        if ($employeeRole) {
                            $user->roles()->attach($employeeRole->id);
                            Log::info('Employee role attached: ' . $employeeRole->id);
                        }

                        // Create Employee Detail
                        $employeeDetail = new EmployeeDetails();
                        $employeeDetail->user_id = $user->id;
                        $employeeDetail->employee_id = $employee['employee_id'];
                        $employeeDetail->address = $employee['city'] ?? null;
                        $employeeDetail->department_id = $departmentId;


                        $existingOffice = CompanyAddress::where('company_id', $companyId)
                            ->where('address', $employee['primary_area_name'])
                            ->first();
                        if ($existingOffice) {
                            $employeeDetail->company_address_id = $existingOffice->id;
                        }
                        // Properly format date fields
                        if (!empty($employee['joining_date'])) {
                            try {
                                // Try different date formats
                                if (strtotime($employee['joining_date'])) {
                                    // If it's a valid date string, convert to Y-m-d format
                                    $employeeDetail->joining_date = Carbon::parse($employee['joining_date'])->format('Y-m-d');
                                    Log::info('Parsed joining_date: ' . $employeeDetail->joining_date);
                                } else {
                                    Log::warning('Invalid joining_date format: ' . $employee['joining_date']);
                                }
                            } catch (\Exception $e) {
                                Log::error('Error parsing joining_date: ' . $e->getMessage());
                                // Skip setting the date if it's invalid
                            }
                        }

                        if (!empty($employee['date_of_birth'])) {
                            try {
                                // Try different date formats
                                if (strtotime($employee['date_of_birth'])) {
                                    // If it's a valid date string, convert to Y-m-d format
                                    $employeeDetail->date_of_birth = Carbon::parse($employee['date_of_birth'])->format('Y-m-d');
                                    Log::info('Parsed date_of_birth: ' . $employeeDetail->date_of_birth);
                                } else {
                                    Log::warning('Invalid date_of_birth format: ' . $employee['date_of_birth']);
                                }
                            } catch (\Exception $e) {
                                Log::error('Error parsing date_of_birth: ' . $e->getMessage());
                                // Skip setting the date if it's invalid
                            }
                        }

                        // Log before save
                        Log::info('About to save employee details: ' . json_encode($employeeDetail->toArray()));

                        $employeeDetail->save();

                        // Log success
                        Log::info('Employee detail saved for user ID: ' . $user->id);

                        $syncCount++;
                    } catch (\Exception $ex) {
                        // Log any errors that occur during user/employee creation
                        Log::error('Error creating employee: ' . $ex->getMessage());
                        Log::error('Stack trace: ' . $ex->getTraceAsString());
                    }
                } else {
                    // Check if a user with this email already exists for this company
                    $user = $existingEmployee->user_id;

                    $departmentId = null;
                    if (!empty($employee['department_id'])) {
                        $department = Team::where('team_name', $employee['department_name'])->first();
                        if ($department) {
                            $departmentId = $department->id;
                        }
                    }

                    // Create Employee Detail
                    $employeeDetail = $existingEmployee;
                    $employeeDetail->employee_id = $employee['employee_id'];
                    $employeeDetail->address = $employee['city'] ?? null;
                    $employeeDetail->department_id = $departmentId;


                    $existingOffice = CompanyAddress::where('company_id', $companyId)
                        ->where('address', $employee['primary_area_name'])
                        ->first();
                    if ($existingOffice) {
                        $employeeDetail->company_address_id = $existingOffice->id;
                    }
                    // Properly format date fields
                    if (!empty($employee['joining_date'])) {
                        try {
                            // Try different date formats
                            if (strtotime($employee['joining_date'])) {
                                // If it's a valid date string, convert to Y-m-d format
                                $employeeDetail->joining_date = Carbon::parse($employee['joining_date'])->format('Y-m-d');
                                Log::info('Parsed joining_date: ' . $employeeDetail->joining_date);
                            } else {
                                Log::warning('Invalid joining_date format: ' . $employee['joining_date']);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error parsing joining_date: ' . $e->getMessage());
                            // Skip setting the date if it's invalid
                        }
                    }

                    if (!empty($employee['date_of_birth'])) {
                        try {
                            // Try different date formats
                            if (strtotime($employee['date_of_birth'])) {
                                // If it's a valid date string, convert to Y-m-d format
                                $employeeDetail->date_of_birth = Carbon::parse($employee['date_of_birth'])->format('Y-m-d');
                                Log::info('Parsed date_of_birth: ' . $employeeDetail->date_of_birth);
                            } else {
                                Log::warning('Invalid date_of_birth format: ' . $employee['date_of_birth']);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error parsing date_of_birth: ' . $e->getMessage());
                            // Skip setting the date if it's invalid
                        }
                    }
                    $employeeDetail->save();

                    $syncCount++;



                    Log::info('Employee already exists: ' . $employee['employee_id']);
                }
            }

            // Record history
            $syncHistory = $this->recordSyncHistory($companyId, $userId, $syncCount);
            Log::info('Sync history recorded: ' . json_encode($syncHistory->toArray()));

            return $syncCount;
        } catch (\Exception $e) {
            Log::error('Employee sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
            'sync_type' => 'employee',
            'total_synced' => $syncCount,
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);

        return $history;
    }
}
