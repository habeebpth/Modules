<?php

namespace Modules\Payroll\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Helper\Reply;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Payroll\Entities\PayrollCycle;
use Modules\Payroll\Http\Requests\StoreSalary;
use App\Http\Controllers\AccountBaseController;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Holiday;
use Modules\Payroll\Entities\EmployeeSalaryGroup;
use Modules\Payroll\Entities\EmployeePayrollCycle;
use Modules\Payroll\Entities\EmployeeMonthlySalary;
use Modules\Payroll\Entities\PayrollCurrencySetting;
use Modules\Payroll\DataTables\payrollAttentanceSummaryDataTable;
use Modules\Payroll\Entities\EmployeeVariableComponent;
use Modules\Payroll\Entities\PayrollLeaveSalaryCalculation;
use Modules\Payroll\Entities\PayrollSetting;
use Modules\Payroll\Http\Requests\StoreEmployyeMonthlySalary;

class payrollAttentanceSummaryController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.AttentanceSummary';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PayrollSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(payrollAttentanceSummaryDataTable $dataTable)
    {
        $viewPermission = user()->permission('manage_employee_salary');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        $this->payrollCycles = PayrollCycle::all();

        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');



        $this->PayrollLeaveSalaryCalculation = PayrollLeaveSalaryCalculation::get();
        return $dataTable->render('payroll::attentance-summary.index', $this->data);
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->company_id = user()->company_id;
        return view('payroll::attentance-summary.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $month = (int) $request->month;
        $year = (int) $request->year;
        $firstDayOfMonth = Carbon::parse("$year-$month-01")->startOfMonth();
        $lastDayOfMonth = Carbon::parse("$year-$month-01")->endOfMonth();
        $formattedMonth = sprintf('%04d-%02d', $year, $month);
        $companyId = user()->company_id;
        $sickHalfLeaveId = LeaveType::where('company_id', $companyId)->where('type_name', 'Sick Half')->value('id');
        $sickFullLeaveId = LeaveType::where('company_id', $companyId)->where('type_name', 'Sick Full')->value('id');
        $employees = User::allEmployees();
        $totalDaysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        foreach ($employees as $employee) {
            $holidayDates = Holiday::where('company_id', $companyId)
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->count();

            $approvedLeaves = Leave::where('user_id', $employee->id)
                ->whereBetween('leave_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('status', 'approved')
                ->get();
            $presentDates = Attendance::where('user_id', $employee->id)
                ->whereYear('clock_in_time', $year)
                ->whereMonth('clock_in_time', $month)
                ->whereNotNull('clock_in_time')
                ->whereNotExists(function ($query) use ($companyId) {
                    $query->select(DB::raw(1))
                        ->from('holidays')
                        ->whereColumn('holidays.date', 'attendances.clock_in_time')
                        ->where('holidays.company_id', $companyId);
                })
                ->whereNotExists(function ($query) use ($employee, $firstDayOfMonth, $lastDayOfMonth) {
                    $query->select(DB::raw(1))
                        ->from('leaves')
                        ->whereColumn('leaves.leave_date', 'attendances.clock_in_time')
                        ->where('leaves.user_id', $employee->id)
                        ->whereIn('duration', ['full_day', 'single', 'multiple'])
                        ->whereBetween('leaves.leave_date', [$firstDayOfMonth, $lastDayOfMonth])
                        ->where('leaves.status', 'approved');
                })

                ->count();

            $totalLeave = $approvedLeaves->sum(fn ($leave) => in_array($leave->duration, ['full_day', 'single', 'multiple']) ? 1 : 0.5);
            $totalLeaves = $approvedLeaves
                ->whereIn('duration', ['full_day', 'single', 'multiple'])
                ->count();

            $totalAbsent = max($totalDaysInMonth - ($presentDates + $totalLeaves + $holidayDates), 0);

            $sickFullLeaves = $approvedLeaves->where('leave_type_id', $sickFullLeaveId)->whereIn('duration', ['full_day', 'single'])->count();
            $sickHalfLeaves = $approvedLeaves->where('leave_type_id', $sickHalfLeaveId)->whereIn('duration', ['first_half', 'second_half', 'half day'])->count();

            $existingRecord = PayrollLeaveSalaryCalculation::where('employee_id', $employee->id)
                ->where('month', $formattedMonth)
                ->where('year', $year)
                ->first();

            if ($existingRecord) {
                $existingRecord->update([
                    'company_id' => $companyId,
                    'no_of_days_in_month' => 0,
                    'no_of_months_in_year' => 0,
                    'sl_full_pay' => $sickFullLeaves,
                    'sl_half_pay' => $sickHalfLeaves,
                    'taken_leave' => $totalLeave,
                    'absent' => $totalAbsent,
                    'combo_offs' => 0,
                    'total_leave_earned' => 0,
                    'opening_leave_balance' => 0,
                    'closing_leave_balance' => 0,
                    'opening_excess_leave' => 0,
                    'closing_excess_leave' => 0,
                    'excess_leave_taken' => 0,
                    'days_worked' => $presentDates,
                ]);
            } else {
                $PayrollLeaveSalaryCalculation = new PayrollLeaveSalaryCalculation();
                $PayrollLeaveSalaryCalculation->company_id = $companyId;
                $PayrollLeaveSalaryCalculation->employee_id = $employee->id;
                $PayrollLeaveSalaryCalculation->month = $formattedMonth;
                $PayrollLeaveSalaryCalculation->year = $year;
                $PayrollLeaveSalaryCalculation->no_of_days_in_month = 0;
                $PayrollLeaveSalaryCalculation->no_of_months_in_year = 0;
                $PayrollLeaveSalaryCalculation->sl_full_pay = $sickFullLeaves;
                $PayrollLeaveSalaryCalculation->sl_half_pay = $sickHalfLeaves;
                $PayrollLeaveSalaryCalculation->taken_leave = $totalLeave;
                $PayrollLeaveSalaryCalculation->absent = $totalAbsent;
                $PayrollLeaveSalaryCalculation->combo_offs = 0;
                $PayrollLeaveSalaryCalculation->total_leave_earned = 0;
                $PayrollLeaveSalaryCalculation->opening_leave_balance = 0;
                $PayrollLeaveSalaryCalculation->closing_leave_balance = 0;
                $PayrollLeaveSalaryCalculation->opening_excess_leave = 0;
                $PayrollLeaveSalaryCalculation->closing_excess_leave = 0;
                $PayrollLeaveSalaryCalculation->excess_leave_taken = 0;
                $PayrollLeaveSalaryCalculation->days_worked = $presentDates;
                $PayrollLeaveSalaryCalculation->save();
            }
        }

        return Reply::successWithData(__('messages.payrollAttentanceSummaryAddedSuccessfully'), [
            'redirectUrl' => route('attentance-summary.index')
        ]);
    }



    public function summaryData($request)
    {
        // dd($request->all());
        $employees = User::with(
            [
                'employeeDetail.designation:id,name',
                'attendance' => function ($query) use ($request) {
                    $startOfMonth = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
                    $officestartTimeDB = Carbon::createFromFormat('Y-m-d H:i:s', $startOfMonth, company()->timezone);
                    $startOfMonth = $startOfMonth->subMinutes($officestartTimeDB->offset / 60);

                    $endOfMonth = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
                    $officeEndTimeDB = Carbon::createFromFormat('Y-m-d H:i:s', $endOfMonth, company()->timezone);
                    $endOfMonth = $endOfMonth->subMinutes($officeEndTimeDB->offset / 60);

                    $query->whereBetween('attendances.clock_in_time', [$startOfMonth, $endOfMonth]);

                    // $query->orwhereRaw('MONTH(attendances.clock_in_time) = ?', [$request->month])
                    // ->orwhereRaw('YEAR(attendances.clock_in_time) = ?', [$request->year]);

                    // $query->where('attendances.added_by', user()->id);
                    // $query->where('attendances.user_id', user()->id);
                },
                'leaves' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(leaves.leave_date) = ?', [$request->month])
                        ->whereRaw('YEAR(leaves.leave_date) = ?', [$request->year])
                        ->where('status', 'approved');
                },
                'shifts' => function ($query) use ($request) {
                    $query->whereRaw('MONTH(employee_shift_schedules.date) = ?', [$request->month])
                        ->whereRaw('YEAR(employee_shift_schedules.date) = ?', [$request->year]);
                },
                'leaves.type',
                'shifts.shift',
                'attendance.shift'
            ]
        )->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'employee_details.department_id', 'users.image')
            ->onlyEmployee()
            ->groupBy('users.id');

        if ($request->department != 'all') {
            $employees = $employees->where('employee_details.department_id', $request->department);
        }

        if ($request->designation != 'all') {
            $employees = $employees->where('employee_details.designation_id', $request->designation);
        }

        if ($request->userId != 'all') {
            $employees = $employees->where('users.id', $request->userId);
        }

        // $employees = $employees->where('users.id', user()->id);

        $employees = $employees->get();
        // dd($employees);
        $user = user();
        $this->holidays = Holiday::whereRaw('MONTH(holidays.date) = ?', [$request->month])->whereRaw('YEAR(holidays.date) = ?', [$request->year])->get();

        $final = [];
        $holidayOccasions = [];
        $leaveReasons = [];

        $this->daysInMonth = Carbon::parse('01-' . $request->month . '-' . $request->year)->daysInMonth;
        $now = now()->timezone($this->company->timezone);
        $requestedDate = Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year))->endOfMonth();

        foreach ($employees as $employee) {

            $dataBeforeJoin = null;

            $dataTillToday = array_fill(1, $now->copy()->format('d'), 'Absent');
            $dataTillRequestedDate = array_fill(1, (int)$this->daysInMonth, 'Absent');
            $daysTofill = ((int)$this->daysInMonth - (int)$now->copy()->format('d'));

            if (($now->copy()->format('d') != $this->daysInMonth) && !$requestedDate->isPast()) {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), '-');
            } else {
                $dataFromTomorrow = array_fill($now->copy()->addDay()->format('d'), (($daysTofill >= 0 ? $daysTofill : 0)), 'Absent');
            }

            if (!$requestedDate->isPast()) {
                $final[$employee->id . '#' . $employee->name] = array_replace($dataTillToday, $dataFromTomorrow);
            } else {
                $final[$employee->id . '#' . $employee->name] = array_replace($dataTillRequestedDate, $dataFromTomorrow);
            }

            $shiftScheduleCollection = $employee->shifts->keyBy('date');


            foreach ($employee->shifts as $shifts) {
                if ($shifts->shift->shift_name == 'Day Off') {
                    $final[$employee->id . '#' . $employee->name][$shifts->date->day] = 'Day Off';
                }
            }

            $firstAttendanceProcessed = [];

            foreach ($employee->attendance as $attendance) {
                $clockInTimeUTC = $attendance->clock_in_time->timezone(company()->timezone)->toDateTimeString();
                $clockInTime = Carbon::createFromFormat('Y-m-d H:i:s', $clockInTimeUTC, 'UTC');
                $startOfDayKey = $clockInTime->startOfDay()->toDateTimeString();

                $shiftSchedule = $shiftScheduleCollection[$startOfDayKey] ?? null;

                if ($shiftSchedule) {
                    $shift = $shiftSchedule->shift;
                    $shiftStartTime = Carbon::parse($clockInTime->toDateString() . ' ' . $shift->office_start_time);
                    $shiftEndTime = Carbon::parse($clockInTime->toDateString() . ' ' . $shift->office_end_time);

                    // Determine if the attendance is within the shift time, the previous day's shift, or otherwise
                    $isWithinShift = $clockInTime->between($shiftStartTime, $shiftEndTime);
                    $isPreviousShift = $clockInTime->betweenIncluded($shiftStartTime->subDay(), $shiftEndTime->subDay());
                    $isAssignedShift = $attendance->employee_shift_id == $shift->id;
                } else {
                    $isWithinShift = $isPreviousShift = $isAssignedShift = false;
                }

                if (!isset($isHalfDay[$employee->id][$startOfDayKey]) && !isset($isLate[$employee->id][$startOfDayKey])) {
                    $isHalfDay[$employee->id][$startOfDayKey] = $isLate[$employee->id][$startOfDayKey] = false;
                }

                // Check if this is the first attendance of the day for this employee
                if (!isset($firstAttendanceProcessed[$employee->id][$startOfDayKey])) {
                    $firstAttendanceProcessed[$employee->id][$startOfDayKey] = true; // Mark as processed

                    // Apply "half day" or "late" logic only if it's the first attendance
                    $isHalfDay[$employee->id][$startOfDayKey] = $attendance->half_day == 'yes';
                    $isLate[$employee->id][$startOfDayKey] = $attendance->late == 'yes';
                }

                $iconClassKey = $isHalfDay[$employee->id][$startOfDayKey] ? 'star-half-alt text-red' : ($isLate[$employee->id][$startOfDayKey] ? 'exclamation-circle text-warning' : 'check text-success');

                // Tooltip title based on attendance status or presence
                $tooltipTitle = $attendance->employee_shift_id ? $attendance->shift->shift_name : __('app.present');

                // Construct the attendance HTML once
                $attendanceHtml = "<a href=\"javascript:;\" data-toggle=\"tooltip\" data-original-title=\"{$tooltipTitle}\" class=\"view-attendance\" data-attendance-id=\"{$attendance->id}\"><i class=\"fa fa-{$iconClassKey}\"></i></a>";

                // Determine the day to assign the attendanceHtml
                if ($isWithinShift || $isAssignedShift || $isPreviousShift) {
                    $dayToAssign = $isPreviousShift ? $clockInTime->copy()->subDay()->day : $clockInTime->day;
                    $final[$employee->id . '#' . $employee->name][$dayToAssign] = $attendanceHtml;
                } else {
                    $final[$employee->id . '#' . $employee->name][$clockInTime->day] = $attendanceHtml;
                }
            }

            $emplolyeeName = view('components.employee', [
                'user' => $employee
            ]);
            // dd($emplolyeeName);
            $final[$employee->id . '#' . $employee->name][] = $emplolyeeName;
            if ($employee->employeeDetail->joining_date->greaterThan(Carbon::parse(Carbon::parse('01-' . $request->month . '-' . $request->year)))) {
                if ($request->month == $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) {
                    if ($employee->employeeDetail->joining_date->format('d') == '01') {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->format('d'), '-');
                    } else {
                        $dataBeforeJoin = array_fill(1, $employee->employeeDetail->joining_date->subDay()->format('d'), '-');
                    }
                }

                if (($request->month < $employee->employeeDetail->joining_date->format('m') && $request->year == $employee->employeeDetail->joining_date->format('Y')) || $request->year < $employee->employeeDetail->joining_date->format('Y')) {
                    $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
                }
            }

            if (Carbon::parse('01-' . $request->month . '-' . $request->year)->isFuture()) {
                $dataBeforeJoin = array_fill(1, $this->daysInMonth, '-');
            }

            if (!is_null($dataBeforeJoin)) {
                $final[$employee->id . '#' . $employee->name] = array_replace($final[$employee->id . '#' . $employee->name], $dataBeforeJoin);
            }

            foreach ($employee->leaves as $leave) {
                if ($leave->duration == 'half day') {
                    if ($final[$employee->id . '#' . $employee->name][$leave->leave_date->day] == '-' || $final[$employee->id . '#' . $employee->name][$leave->leave_date->day] == 'Absent') {
                        $final[$employee->id . '#' . $employee->name][$leave->leave_date->day] = 'Half Day';
                    }
                } else {
                    $final[$employee->id . '#' . $employee->name][$leave->leave_date->day] = 'Leave';
                    $leaveReasons[$employee->id][$leave->leave_date->day] = $leave->type->type_name;
                }
            }

            foreach ($this->holidays as $holiday) {
                $departmentId = $employee->employeeDetail->department_id;
                $designationId = $employee->employeeDetail->designation_id;
                $employmentType = $employee->employeeDetail->employment_type;


                $holidayDepartment = (!is_null($holiday->department_id_json)) ? json_decode($holiday->department_id_json) : [];
                $holidayDesignation = (!is_null($holiday->designation_id_json)) ? json_decode($holiday->designation_id_json) : [];
                $holidayEmploymentType = (!is_null($holiday->employment_type_json)) ? json_decode($holiday->employment_type_json) : [];

                if (((in_array($departmentId, $holidayDepartment) || $holiday->department_id_json == null) &&
                    (in_array($designationId, $holidayDesignation) || $holiday->designation_id_json == null) &&
                    (in_array($employmentType, $holidayEmploymentType) || $holiday->employment_type_json == null))) {


                    if ($final[$employee->id . '#' . $employee->name][$holiday->date->day] == 'Absent' || $final[$employee->id . '#' . $employee->name][$holiday->date->day] == '-') {
                        $final[$employee->id . '#' . $employee->name][$holiday->date->day] = 'Holiday';
                        $holidayOccasions[$holiday->date->day] = $holiday->occassion;
                    }
                }
            }
        }

        $this->employeeAttendence = $final;
        // dd($this->employeeAttendence);
        $this->holidayOccasions = $holidayOccasions;
        $this->leaveReasons = $leaveReasons;
        // dd($this->leaveReasons);
        $this->weekMap = Holiday::weekMap('D');

        $this->month = $request->month;
        $this->year = $request->year;
        return $this->data;
    }
    public function stored(Request $request)
    {
        $request->validate([
            'month' => 'required|string|',
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $summaryData = $this->summaryData($request);

        $month = (int) $request->month;
        // dd($month);
        $year = (int) $request->year;
        $formattedMonth = sprintf('%04d-%02d', $year, $month);
        $companyId = user()->company_id;
        // dd($formattedMonth);


        foreach ($summaryData['employeeAttendence'] as $employeeId => $attendance) {
            // dd($attendance);
            if (!is_array($attendance)) {
                Log::error('Invalid attendance data for employee', [
                    'employeeId' => $employeeId,
                    'attendance' => $attendance,
                ]);
                continue; // Skip this iteration
            }

            $totalLeave = 0;
            $totalAbsent = 0;
            $totalPresent = 0;
            $totalHoliday = 0;
            $totalHalfDay = 0;
            $totalDayoff = 0;
            $totalCasualleave = 0;
            $totalSickFull = 0;
            $totalSickHalf = 0;
            $userId = explode('#', $employeeId);
            $userId = $userId[0];
            // dd($summaryData);

            foreach ($attendance as $day => $status) {
                if ($day + 1 <= count($attendance)) {
                    if ($status == 'Leave') {
                        // dd($summaryData['leaveReasons'][$userId]);
                        if ($summaryData['leaveReasons'][$userId][$day] == 'Casual') {
                            $totalCasualleave++;
                        }
                        if ($summaryData['leaveReasons'][$userId][$day] == 'Sick Full') {
                            $totalSickFull++;
                        }
                        if ($summaryData['leaveReasons'][$userId][$day] == 'Sick Half') {
                            $totalSickHalf++;
                        }
                        $totalLeave++;
                    } elseif ($status == 'Absent') {
                        $totalAbsent++;
                    } elseif ($status == 'Day Off') {
                        $totalDayoff++;
                    } elseif ($status == 'Half Day') {
                        dd($summaryData['leaveReasons']);
                        $totalHalfDay++;
                    } elseif ($status == 'Holiday') {
                        $totalHoliday++;
                    } elseif ($status != '-') {
                        $totalPresent++;
                    }
                }
            }
            $existingRecord = PayrollLeaveSalaryCalculation::where('employee_id', $userId)
                ->where('month', $formattedMonth)
                ->where('year', $year)
                ->first();

            if ($existingRecord) {
                // Update the existing record
                $existingRecord->update([
                    'company_id' => $companyId,
                    'no_of_days_in_month' => Carbon::parse("$year-$month-01")->daysInMonth,
                    'no_of_months_in_year' => 0,
                    'sl_full_pay' => $totalSickFull ?? 0,
                    'sl_half_pay' => $totalSickHalf ?? 0,
                    'taken_leave' => $totalLeave,
                    'absent' => $totalAbsent,
                    'combo_offs' => 0,
                    'total_leave_earned' => 0,
                    'opening_leave_balance' => 0,
                    'closing_leave_balance' => 0,
                    'opening_excess_leave' => 0,
                    'closing_excess_leave' => 0,
                    'excess_leave_taken' => 0,
                    'days_worked' => $totalPresent,
                ]);
            } else {
                // Create a new record
                $PayrollLeaveSalaryCalculation = new PayrollLeaveSalaryCalculation();
                $PayrollLeaveSalaryCalculation->company_id = $companyId;
                $PayrollLeaveSalaryCalculation->employee_id = $userId;
                $PayrollLeaveSalaryCalculation->month = $formattedMonth;
                $PayrollLeaveSalaryCalculation->year = $year;
                $PayrollLeaveSalaryCalculation->no_of_days_in_month = Carbon::parse("$year-$month-01")->daysInMonth;
                $PayrollLeaveSalaryCalculation->no_of_months_in_year = 0;
                $PayrollLeaveSalaryCalculation->sl_full_pay = $totalSickFull ?? 0;
                $PayrollLeaveSalaryCalculation->sl_half_pay = $totalSickHalf ?? 0;
                $PayrollLeaveSalaryCalculation->taken_leave = $totalLeave;
                $PayrollLeaveSalaryCalculation->absent = $totalAbsent;
                $PayrollLeaveSalaryCalculation->combo_offs = 0;
                $PayrollLeaveSalaryCalculation->total_leave_earned = 0;
                $PayrollLeaveSalaryCalculation->opening_leave_balance = 0;
                $PayrollLeaveSalaryCalculation->closing_leave_balance = 0;
                $PayrollLeaveSalaryCalculation->opening_excess_leave = 0;
                $PayrollLeaveSalaryCalculation->closing_excess_leave = 0;
                $PayrollLeaveSalaryCalculation->excess_leave_taken = 0;
                $PayrollLeaveSalaryCalculation->days_worked = $totalPresent;
                $PayrollLeaveSalaryCalculation->save();
            }
        }

        return Reply::successWithData(__('messages.payrollAdvanceAddedSuccessfully'), [
            'redirectUrl' => route('attentance-summary.index')
        ]);
    }



    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

        $this->pageTitle = __('payroll::app.menu.AttentanceSummary');

        $this->SalaryCalculation = PayrollLeaveSalaryCalculation::findOrFail($id);
        $this->employees = User::allEmployees();

        $this->view = 'payroll::attentance-summary.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::attentance-summary.create', $this->data);
    }

    /**
     * Increment
     *
     * @param  mixed $id
     * @return void
     */

    /**
     * UpdateIncrement
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    //phpcs:ignore

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    //phpcs:ignore
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'sl_full_pay',
            'sl_half_pay',
            'taken_leave',
            'absent',
            'combo_offs',
            'total_leave_earned',
            'opening_leave_balance',
            'closing_leave_balance',
            'opening_excess_leave',
            'closing_excess_leave',
            'excess_leave_taken'
        ]);

        $salaryCalculation = PayrollLeaveSalaryCalculation::findOrFail($id);
        $salaryCalculation->update($data);

        return response()->json(['status' => 'success', 'redirectUrl' => route('attentance-summary.index')]);
    }



    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $viewPermission = user()->permission('manage_employee_salary');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        EmployeeMonthlySalary::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
