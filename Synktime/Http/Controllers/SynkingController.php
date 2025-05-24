<?php

namespace Modules\Synktime\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Models\Attendance;
use App\Models\EmployeeDetails;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Modules\Payroll\Entities\OvertimeRequest;
use Modules\Payroll\Entities\OvertimePolicyEmployee;
use App\Models\Holiday;
use Illuminate\Support\Str;
use Modules\Synktime\DataTables\SynkingHistoryDataTable;
use Modules\Synktime\Entities\Configuration;
use Modules\Synktime\Entities\SynkingHistory;

class SynkingController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'synktime::app.menu.SyncingHistory';

        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(SynkingHistoryDataTable $dataTable)
    {
        $viewPermission = user()->permission('manage_employee_salary');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        // Update page title if showing entity syncs
        if (request()->has('show_entity_syncs') && request()->show_entity_syncs === 'true') {
            $this->pageTitle = 'synktime::app.entity_sync';
        }

        $this->SynkingHistory = SynkingHistory::with(['createdBy', 'employee'])->get();

        try {
            return $dataTable->render('synktime::synking-history.index', $this->data);
        } catch (\Exception $e) {
            // Fallback to regular view if datatable fails
            return view('synktime::synking-history.index', $this->data);
        }
    }

    public function updateConfiguration(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'url' => 'required|url',
            'api_key' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'attendance_type' => 'required|string|in:transaction,summary',
        ]);

        // Find the finance setting by ID
        $Configuration = Configuration::findOrFail($id);

        // Update the finance settings
        $Configuration->update([
            'url' => $request->url,
            'api_key' => $request->api_key,
            'username' => $request->username,
            // Only update the password if provided
            'password' => $request->filled('password') ? bcrypt($request->password) : $Configuration->password,
            'attendance_type' => $request->attendance_type,
        ]);

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->company_id = user()->company_id;
        return view('synktime::synking-history.ajax.create', $this->data);
    }

    public function Store(Request $request)
    {
        $config = Configuration::first();
        $url = $config->url;
        $token = $config->api_key;

        // Fix date parsing issues with better error handling
        try {
            $fromDate = $request->from_date ? companyToYmd($request->from_date) : now()->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('From date parsing error: ' . $e->getMessage());
            $fromDate = now()->format('Y-m-d'); // Fallback to current date
        }

        try {
            $toDate = $request->to_date ? companyToYmd($request->to_date) : now()->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error('To date parsing error: ' . $e->getMessage());
            $toDate = now()->format('Y-m-d'); // Fallback to current date
        }

        $companyId = user()->company_id;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->withoutVerifying()->get($url, [
                "date1" => $fromDate,
                "date2" => $toDate,
                "employee_code" => "",
                "project_id" => ""
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $syncCount = 0;

                if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
                    foreach ($data['data'] as $employee) {
                        if (!isset($employee['employee_id']) || !isset($employee['summary']) || !is_array($employee['summary'])) {
                            \Log::warning('Missing or invalid employee data', ['employee' => $employee]);
                            continue;
                        }

                        $user = EmployeeDetails::where('employee_id', $employee['employee_id'])->first();
                        if ($user) {
                            foreach ($employee['summary'] as $entry) {
                                $date = $entry['date'];
                                $clockIn = !empty($entry['checkin']) ? Carbon::parse($date . ' ' . $entry['checkin'])->format('Y-m-d H:i:s') : null;
                                $clockOut = !empty($entry['checkout']) ? Carbon::parse($date . ' ' . $entry['checkout'])->format('Y-m-d H:i:s') : null;

                                if (!isset($entry['attendance']) || strtolower($entry['attendance']) === 'absent' || (is_null($clockIn) && is_null($clockOut))) {
                                    continue;
                                }

                                $isHoliday = Holiday::where('company_id', $companyId)->where('date', $date)->exists();
                                $existingRequestholiday = OvertimeRequest::where('user_id', $user->user_id)
                                    ->where('date', $date)
                                    ->first();

                                if (!$existingRequestholiday && $isHoliday) {
                                    $userPolicy = OvertimePolicyEmployee::with('policy', 'policy.payCode')->where('user_id', $user->user_id)->first();
                                    $userData = User::find($user->user_id);

                                    $overtimeHours = 0;
                                    $remainingMinutes = 0;
                                    $amount = 0;

                                    if ($userPolicy && isset($userPolicy->policy) && isset($userPolicy->policy->payCode) && $userPolicy->policy->payCode->fixed == 1) {
                                        $amount = ($overtimeHours * $userPolicy->policy->payCode->fixed_amount) + ($remainingMinutes * ($userPolicy->policy->payCode->fixed_amount / 60));
                                    } elseif ($userPolicy && isset($userPolicy->policy) && isset($userPolicy->policy->payCode)) {
                                        $hourlyRate = $userData->employeeDetail->overtime_hourly_rate * $userPolicy->policy->payCode->time;
                                        $amount = ($overtimeHours * $hourlyRate) + ($remainingMinutes * ($hourlyRate / 60));
                                    }

                                    OvertimeRequest::create([
                                        'user_id' => $user->user_id,
                                        'start_date' => $date,
                                        'end_date' => $date,
                                        'date' => $date,
                                        'hours' => $overtimeHours,
                                        'minutes' => $remainingMinutes,
                                        'amount' => $amount,
                                        'overtime_reason' => 'Auto-generated from daily attendance (Holiday)',
                                        'overtime_policy_id' => $userPolicy && isset($userPolicy->overtime_policy_id) ? $userPolicy->overtime_policy_id : null,
                                        'type' => 'draft',
                                        'batch_key' => Str::random(16),
                                    ]);
                                }

                                if (!$isHoliday) {
                                    Attendance::updateOrCreate([
                                        'user_id' => $user->user_id,
                                        'clock_in_time' => $clockIn,
                                    ], [
                                        'company_id' => user()->company_id,
                                        'clock_out_time' => $clockOut,
                                        'working_from' => 'office',
                                        'half_day' => 'no',
                                        'late' => 'no',
                                    ]);
                                    $syncCount++;
                                }

                                // Extract and calculate total working minutes safely
                                $totalWorkingMinutes = 0;
                                if (!empty($entry['total_working_minutes'])) {
                                    $parts = explode(':', $entry['total_working_minutes']);
                                    if (count($parts) >= 2) {
                                        $totalWorkingMinutes = ((int)$parts[0] * 60) + (int)$parts[1];
                                    }
                                }

                                // Initialize variables
                                $amount = 0;
                                $overtimeHours = 0;
                                $remainingMinutes = 0;

                                // Safely calculate amounts if policy exists
                                if ($userPolicy && isset($userPolicy->policy) && isset($userPolicy->policy->payCode)) {
                                    if ($userPolicy->policy->payCode->fixed == 1) {
                                        $amount = ($overtimeHours * $userPolicy->policy->payCode->fixed_amount) + ($remainingMinutes * ($userPolicy->policy->payCode->fixed_amount / 60));
                                    } else {
                                        if (isset($userData) && isset($userData->employeeDetail) && isset($userData->employeeDetail->overtime_hourly_rate)) {
                                            $hourlyRate = $userData->employeeDetail->overtime_hourly_rate * $userPolicy->policy->payCode->time;
                                            $amount = ($overtimeHours * $hourlyRate) + ($remainingMinutes * ($hourlyRate / 60));
                                        }
                                    }
                                }

                                if ($totalWorkingMinutes > 480) {
                                    $overtimeMinutes = $totalWorkingMinutes - 480;
                                    $overtimeHours = floor($overtimeMinutes / 60);
                                    $remainingMinutes = $overtimeMinutes % 60;

                                    $existingRequest = OvertimeRequest::where('user_id', $user->user_id)
                                        ->where('date', $date)
                                        ->first();

                                    if (!$existingRequest) {
                                        OvertimeRequest::create([
                                            'user_id' => $user->user_id,
                                            'start_date' => $date,
                                            'end_date' => $date,
                                            'date' => $date,
                                            'hours' => $overtimeHours,
                                            'minutes' => $remainingMinutes,
                                            'amount' => $amount,
                                            'overtime_reason' => 'Auto-generated from daily attendance',
                                            'overtime_policy_id' => $userPolicy && isset($userPolicy->overtime_policy_id) ? $userPolicy->overtime_policy_id : null,
                                            'type' => 'draft',
                                            'batch_key' => Str::random(16),
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    // Create history record for the sync
                    SynkingHistory::create([
                        'company_id' => user()->company_id,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                        'sync_type' => 'attendance',
                        'total_synced' => $syncCount,
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                    ]);
                }
            } else {
                \Log::error('Failed to fetch attendance data', ['response' => $response->body()]);
                return Reply::error(__('messages.updateFailed'));
            }
        } catch (Exception $e) {
            \Log::error('Exception in fetching attendance data', ['error' => $e->getMessage()]);
            return Reply::error(__('messages.updateFailed'));
        }

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->synkingHistory = SynkingHistory::with(['createdBy', 'employee'])->findOrFail($id);
        return view('synktime::synking-history.show', $this->data);
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
