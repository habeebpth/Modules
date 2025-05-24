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
use Carbon\Carbon;
use Modules\Payroll\Http\Requests\Payrollcalculation\ImportRequest;
use Modules\Payroll\Http\Requests\Payrollcalculation\ImportProcessRequest;
use Modules\Payroll\Imports\PayrollcalculationImport;
use Modules\Payroll\Jobs\ImportPayrollcalculationJob;
use Modules\Payroll\Entities\EmployeeSalaryGroup;
use Modules\Payroll\Entities\EmployeePayrollCycle;
use App\Traits\ImportExcel;
use Modules\Payroll\Entities\EmployeeMonthlySalary;
use Modules\Payroll\Entities\PayrollCurrencySetting;
use Modules\Payroll\DataTables\payrollSalaryCalculationDataTable;
use Modules\Payroll\Entities\EmployeeVariableComponent;
use Modules\Payroll\Entities\PayrollLeaveSalaryCalculation;
use Modules\Payroll\Entities\PayrollSetting;
use Modules\Payroll\Http\Requests\StoreEmployyeMonthlySalary;

class payrollSalaryCalculationController extends AccountBaseController
{
    use ImportExcel;
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.SalarayCalculation';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PayrollSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(payrollSalaryCalculationDataTable $dataTable)
    {
        $viewPermission = user()->permission('manage_employee_salary');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        $this->payrollCycles = PayrollCycle::all();

        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        $this->PayrollLeaveSalaryCalculation = PayrollLeaveSalaryCalculation::get();
        return $dataTable->render('payroll::salary-calculation.index', $this->data);
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $this->company_id = user()->company_id;
        return view('payroll::salary-calculation.ajax.create', $this->data);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'month' => 'required|string|size:2', // Ensures month format is 01-12
            'year' => 'required|integer|min:1900|max:2099',
        ]);

        $month = (int) $request->month; // Converts "01" to 1
        $year = (int) $request->year; // Converts "2025" to 2025
        $formattedMonth = sprintf('%04d-%02d', $year, $month); // Ensures YYYY-MM format
        // dd($formattedMonth);
        // Get all active employees
        $employees = User::allEmployees(); // Modify if you have an 'active' status column

        foreach ($employees as $employee) {
            // Check if a record already exists for this employee, month, and year
            $existingRecord = PayrollLeaveSalaryCalculation::where('employee_id', $employee->id)
                ->where('month', $formattedMonth)
                ->where('year', $year)
                ->first();

            if ($existingRecord) {
                // Update the existing record
                $existingRecord->update([
                    'salary_basic' => 0,
                    'salary_spay' => 0,
                    'salary_hra' => 0,
                    'salary_incentive' => 0,
                    'salary_gross' => 0,
                    'salary_net' => 0,
                    'salary_leave' => 0,
                    'salary_advance' => 0,
                    'salary_hra_advance' => 0,
                    'salary_ot' => 0,
                    'total_deduction' => 0
                ]);
            } else {
                // Create a new record
                $PayrollLeaveSalaryCalculation = new PayrollLeaveSalaryCalculation();
                $PayrollLeaveSalaryCalculation->employee_id = $employee->id;
                $PayrollLeaveSalaryCalculation->month = $formattedMonth;
                $PayrollLeaveSalaryCalculation->year = $year;
                $PayrollLeaveSalaryCalculation->no_of_days_in_month = 0;
                $PayrollLeaveSalaryCalculation->no_of_months_in_year = 0;
                $PayrollLeaveSalaryCalculation->salary_basic = 0;
                $PayrollLeaveSalaryCalculation->salary_spay = 0;
                $PayrollLeaveSalaryCalculation->salary_hra = 0;
                $PayrollLeaveSalaryCalculation->salary_incentive = 0;
                $PayrollLeaveSalaryCalculation->salary_gross = 0;
                $PayrollLeaveSalaryCalculation->salary_net = 0;
                $PayrollLeaveSalaryCalculation->salary_leave = 0;
                $PayrollLeaveSalaryCalculation->salary_advance = 0;
                $PayrollLeaveSalaryCalculation->salary_hra_advance = 0;
                $PayrollLeaveSalaryCalculation->salary_ot = 0;
                $PayrollLeaveSalaryCalculation->total_deduction = 0;
                $PayrollLeaveSalaryCalculation->save();
            }
        }

        return Reply::successWithData(__('messages.SalaryCalculationGeneratedSuccessfully'), [
            'redirectUrl' => route('salary-calculation.index')
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

        // dd($id);
        $this->pageTitle = __('payroll::app.menu.SalaryCalculation');

        $this->SalaryCalculation = PayrollLeaveSalaryCalculation::findOrFail($id);
        $this->employees = User::allEmployees();

        $this->view = 'payroll::salary-calculation.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::salary-calculation.create', $this->data);
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
                'salary_basic',
                'salary_spay',
                'salary_hra',
                'salary_incentive',
                'salary_gross',
                'salary_net',
                'salary_leave',
                'salary_advance',
                'salary_hra_advance',
                'salary_ot',
                'total_deduction'
        ]);

        $salaryCalculation = PayrollLeaveSalaryCalculation::findOrFail($id);
        $salaryCalculation->update($data);

        return response()->json(['status' => 'success', 'redirectUrl' => route('salary-calculation.index')]);
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
    public function importPayroll()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.menu.SalarayCalculation');

        $this->view = 'payroll::salary-calculation.ajax.import';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::salary-calculation.create', $this->data);
    }
    public function importStore(ImportRequest $request)
    {
        $this->importFileProcess($request, PayrollcalculationImport::class);
        $view = view('payroll::salary-calculation.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }
    public function importProcess(ImportProcessRequest $request)
    {
        $batch = $this->importJobProcess($request, PayrollcalculationImport::class, ImportPayrollcalculationJob::class);

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }

}
