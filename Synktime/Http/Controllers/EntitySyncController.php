<?php

namespace Modules\Synktime\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAuth;
use App\Models\EmployeeDetails;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use Modules\Synktime\Entities\Configuration;
use Modules\Synktime\Entities\SynkingHistory;
use Modules\Synktime\Services\DepartmentSyncService;
use Modules\Synktime\Services\AreaSyncService;
use Modules\Synktime\Services\EmployeeSyncService;
use Modules\Synktime\Services\AttendanceSyncService;
use Modules\Synktime\DataTables\SynkingHistoryDataTable;

class EntitySyncController extends AccountBaseController
{
    protected $departmentSyncService;
    protected $areaSyncService;
    protected $employeeSyncService;
    protected $attendanceSyncService;

    public function __construct(
        DepartmentSyncService $departmentSyncService,
        AreaSyncService $areaSyncService,
        EmployeeSyncService $employeeSyncService,
        AttendanceSyncService $attendanceSyncService
    ) {
        parent::__construct();
        $this->pageTitle = 'synktime::app.menu.EntitySync';

        $this->departmentSyncService = $departmentSyncService;
        $this->areaSyncService = $areaSyncService;
        $this->employeeSyncService = $employeeSyncService;
        $this->attendanceSyncService = $attendanceSyncService;

        $this->middleware(function ($request, $next) {
            // Add your permission checks here if needed
            return $next($request);
        });
    }

    public function showSyncOptions(SynkingHistoryDataTable $dataTable = null)
    {
        // Get recent sync history for all entity types
        $this->recentSyncs = SynkingHistory::whereIn('sync_type', ['department', 'area', 'employee', 'attendance'])
            ->with('createdBy')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // If directly accessing entity-sync, we don't need the dataTable
        return view('synktime::entity-sync.options', $this->data);
    }

    /**
     * Department Syncing
     */
    public function syncDepartments(Request $request)
    {
        $config = Configuration::first();

        $url = $config->url;
        $username = $config->username;
        $password = $config->password;
        $token = $config->api_key;
        $companyId = user()->company_id;

        // API authentication
        $loginResponse = Http::withoutVerifying()->post($url . '/api/api-token-auth', [
            'email' => $username,
            'password' => $password,
        ]);

        if ($loginResponse->successful()) {
            if (isset($loginResponse['authorisation']) && isset($loginResponse['authorisation']['token'])) {
                $token = $loginResponse['authorisation']['token'];
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->withoutVerifying()->get($url . '/api/v1/get-departments');

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                    $syncCount = $this->departmentSyncService->syncDepartments(
                        $data['data'],
                        $companyId,
                        auth()->id()
                    );

                    return Reply::success(__('synktime::app.syncSuccess') . " {$syncCount} departments synced.");
                }
            } else {
                Log::error('Failed to fetch department data', ['response' => $response->body()]);
                return Reply::error(__('synktime::app.syncFailed'));
            }
        } catch (Exception $e) {
            Log::error('Exception in fetching department data', ['error' => $e->getMessage()]);
            return Reply::error(__('synktime::app.syncFailed'));
        }

        return Reply::error(__('synktime::app.syncFailed'));
    }

    /**
     * Area Syncing (Areas to Offices)
     */
    public function syncAreas(Request $request)
    {
        $config = Configuration::first();
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;
        $token = $config->api_key;
        $companyId = user()->company_id;

        // API authentication
        $loginResponse = Http::withoutVerifying()->post($url . '/api/api-token-auth', [
            'email' => $username,
            'password' => $password,
        ]);

        if ($loginResponse->successful()) {
            if (isset($loginResponse['authorisation']) && isset($loginResponse['authorisation']['token'])) {
                $token = $loginResponse['authorisation']['token'];
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->withoutVerifying()->get($url . '/api/v1/get-areas');

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                    $syncCount = $this->areaSyncService->syncAreas(
                        $data['data'],
                        $companyId,
                        auth()->id()
                    );

                    return Reply::success(__('synktime::app.syncSuccess') . " {$syncCount} areas synced.");
                }
            } else {
                Log::error('Failed to fetch area data', ['response' => $response->body()]);
                return Reply::error(__('synktime::app.syncFailed'));
            }
        } catch (Exception $e) {
            Log::error('Exception in fetching area data', ['error' => $e->getMessage()]);
            return Reply::error(__('synktime::app.syncFailed'));
        }

        return Reply::error(__('synktime::app.syncFailed'));
    }

    /**
     * Employee Syncing with direct processing for debugging
     */
    public function syncEmployees(Request $request)
    {
        $config = Configuration::first();
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;
        $token = $config->api_key;
        $companyId = user()->company_id;

        // API authentication
        $loginResponse = Http::withoutVerifying()->post($url . '/api/api-token-auth', [
            'email' => $username,
            'password' => $password,
        ]);

        if ($loginResponse->successful()) {
            if (isset($loginResponse['authorisation']) && isset($loginResponse['authorisation']['token'])) {
                $token = $loginResponse['authorisation']['token'];
            }
        }

        try {
            // Get employee role
            $employeeRole = Role::where('name', 'employee')->first();

            // Make API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->withoutVerifying()->get($url . '/api/v1/get-employees');

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                    $syncCount = $this->employeeSyncService->syncEmployees(
                        $data['data'],
                        $companyId,
                        auth()->id()
                    );

                    return Reply::success(__('synktime::app.syncSuccess') . " {$syncCount} employees synced.");
                }
            } else {
                Log::error('Failed to fetch employee data', ['response' => $response->body()]);
                return Reply::error(__('synktime::app.syncFailed'));
            }
        } catch (Exception $e) {
            Log::error('Exception in fetching employee data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Reply::error(__('synktime::app.syncFailed') . ': ' . $e->getMessage());
        }

        return Reply::error(__('synktime::app.syncFailed'));
    }

    /**
     * Attendance Syncing
     */
    public function syncAttendance(Request $request)
    {
        $config = Configuration::first();
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;
        $token = $config->api_key;
        $companyId = user()->company_id;

        // Get date range for syncing (default to current month if not specified)
        $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        // API authentication - using the configuration values
        $loginResponse = Http::withoutVerifying()->post($url . '/api/api-token-auth', [
            'email' => $username,
            'password' => $password,
        ]);

        $token = $config->api_key; // Default to the configured API key

        if ($loginResponse->successful()) {
            if (isset($loginResponse['authorisation']) && isset($loginResponse['authorisation']['token'])) {
                $token = $loginResponse['authorisation']['token'];
                Log::info('Successfully authenticated with API, got new token');
            } else {
                Log::warning('Authentication response missing expected token structure',
                    ['response' => mb_substr($loginResponse->body(), 0, 500)]
                );
            }
        } else {
            Log::warning('API authentication failed, using configured API key as fallback',
                ['status' => $loginResponse->status()]
            );
        }

        try {
            // Make API request to get daily attendance report with correct URL and parameters
            // Using the exact URL format you provided: /api/v1/get-daily-report?date1=2025-05-01&date2=2025-05-14&employee_code=&project_id=
            $apiUrl = $url . '/api/v1/get-daily-report';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->withoutVerifying()->get($apiUrl, [
                'date1' => $fromDate,
                'date2' => $toDate,
                'employee_code' => '',
                'project_id' => ''
            ]);

            if ($response->successful()) {
                // Log the raw response for debugging
                Log::info('Raw API response for attendance sync:', ['response' => mb_substr($response->body(), 0, 1000) . '...']); // Log first 1000 chars to avoid huge logs

                $data = $response->json();

                // Validate the response structure
                if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data']) && is_array($data['data'])) {
                    // Process data and handle potential format issues
                    $validData = [];

                    foreach ($data['data'] as $employee) {
                        // Ensure each employee record has minimum required fields
                        if (is_array($employee) && isset($employee['employee_id'])) {
                            $validData[] = $employee;
                        } else {
                            Log::warning('Invalid employee record format in API response', ['record' => $employee]);
                        }
                    }

                    if (empty($validData)) {
                        Log::error('No valid employee records found in API response');
                        return Reply::error(__('synktime::app.noValidRecordsFound'));
                    }

                    $syncCount = $this->attendanceSyncService->syncAttendance(
                        $validData,
                        $companyId,
                        auth()->id()
                    );

                    return Reply::success(__('synktime::app.syncSuccess') . " {$syncCount} attendance records synced.");
                } else {
                    Log::error('Invalid API response format for attendance data', ['response' => mb_substr(json_encode($data), 0, 1000) . '...']);
                    return Reply::error(__('synktime::app.invalidAPIResponse'));
                }
            } else {
                Log::error('Failed to fetch attendance data', [
                    'status_code' => $response->status(),
                    'url' => $apiUrl,
                    'params' => [
                        'date1' => $fromDate,
                        'date2' => $toDate,
                        'employee_code' => '',
                        'project_id' => ''
                    ],
                    'response' => mb_substr($response->body(), 0, 1000) . '...' // Log first 1000 chars
                ]);
                return Reply::error(__('synktime::app.syncFailed') . ' (Status: ' . $response->status() . ')');
            }
        } catch (Exception $e) {
            Log::error('Exception in fetching attendance data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Reply::error(__('synktime::app.syncFailed') . ': ' . $e->getMessage());
        }

        return Reply::error(__('synktime::app.syncFailed'));
    }

    /**
     * Attendance Dashboard
     */
    public function attendanceDashboard()
    {
        $this->pageTitle = 'synktime::app.attendance_dashboard';

        // Get attendance statistics
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalEmployees = User::where('company_id', user()->company_id)->count();

        $presentDays = \App\Models\Attendance::where('company_id', user()->company_id)
            ->whereMonth('clock_in_time', $currentMonth)
            ->whereYear('clock_in_time', $currentYear)
            ->count();

        $lateDays = \App\Models\Attendance::where('company_id', user()->company_id)
            ->whereMonth('clock_in_time', $currentMonth)
            ->whereYear('clock_in_time', $currentYear)
            ->where('late', 'yes')
            ->count();

        // For absent days, we would need to calculate based on working days and present days
        // This is a simplified calculation
        $workingDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear) - 8; // Assuming 8 weekend days
        $absentDays = ($workingDays * $totalEmployees) - $presentDays;

        $this->totalEmployees = $totalEmployees;
        $this->presentDays = $presentDays;
        $this->lateDays = $lateDays;
        $this->absentDays = max(0, $absentDays); // Ensure no negative value

        // Get recent attendance records
        $this->recentAttendance = \App\Models\Attendance::where('company_id', user()->company_id)
            ->with('user')
            ->orderBy('clock_in_time', 'desc')
            ->take(10)
            ->get();

        // Additional data for dashboard can be added here

        return view('synktime::entity-sync.attendance-dashboard', $this->data);
    }
}
