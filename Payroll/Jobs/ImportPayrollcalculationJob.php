<?php

namespace Modules\Payroll\Jobs;

use App\Traits\ExcelImportable;
use App\Traits\UniversalSearchTrait;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Modules\Payroll\Entities\PayrollLeaveSalaryCalculation;
use Exception;

class ImportPayrollcalculationJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UniversalSearchTrait;
    use ExcelImportable;

    private $row;
    private $columns;
    private $company;

    public function __construct($row, $columns, $company = null)
    {
        $this->row = $row;
        $this->columns = $columns;
        $this->company = $company;
    }

    public function uniqueId()
    {
        return md5(json_encode($this->row)); // Ensures uniqueness for each row
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $companyId = $this->company?->id;

            if (!$companyId) {
                Log::error("Company ID not found for payroll import.");
                $this->failJobWithMessage(__('messages.companyIdNotFound'));
                DB::rollBack();
                return;
            }

            $employeeName = $this->getColumnValue('employee');
            $employee = User::where('company_id', $companyId)->where('name', $employeeName)->first();

            if (!$employee) {
                Log::error("Invalid employee: $employeeName");
                DB::rollBack();
                return;
            }

            $payroll = new PayrollLeaveSalaryCalculation();
            $payroll->company_id = $companyId;
            $payroll->employee_id = $employee->id;
            $payroll->employee_type = $this->getColumnValue('employee_type');
            $payroll->employee_grade = $this->getColumnValue('employee_grade');

            $monthValue = $this->getColumnValue('month');
            try {
                if (preg_match('/^\d{2}-\d{4}$/', $monthValue)) {
                    $payroll->month = Carbon::createFromFormat('m-Y', $monthValue)->format('Y-m');
                } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $monthValue)) {
                    $payroll->month = Carbon::createFromFormat('d-m-Y', $monthValue)->format('Y-m');
                } elseif (preg_match('/^\d{4}-\d{2}$/', $monthValue)) {
                    $payroll->month = $monthValue;
                } else {
                    throw new Exception("Invalid month format: $monthValue");
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                DB::rollBack();
                return;
            }

            $payroll->year = $this->getColumnValue('year');
            $payroll->no_of_days_in_month = $this->getColumnValue('no_of_days_in_month');
            $payroll->no_of_months_in_year = $this->getColumnValue('no_of_months_in_year');

            // Store leave data safely
            $payroll->sl_full_pay = $this->getColumnValue('sl_full_pay') ?? 0;
            $payroll->sl_half_pay = $this->getColumnValue('sl_half_pay') ?? 0;
            $payroll->taken_leave = $this->getColumnValue('taken_leave') ?? 0;
            $payroll->absent = $this->getColumnValue('absent') ?? 0;
            $payroll->combo_offs = $this->getColumnValue('combo_offs') ?? 0;
            $payroll->total_leave_earned = $this->getColumnValue('total_leave_earned') ?? 0;

            // Salary details
            $payroll->salary_basic = $this->getColumnValue('salary_basic') ?? 0;
            $payroll->salary_spay = $this->getColumnValue('salary_spay') ?? 0;
            $payroll->salary_hra = $this->getColumnValue('salary_hra') ?? 0;
            $payroll->salary_incentive = $this->getColumnValue('salary_incentive') ?? 0;
            $payroll->salary_gross = $this->getColumnValue('salary_gross') ?? 0;
            $payroll->salary_net = $this->getColumnValue('salary_net') ?? 0;
            $payroll->salary_leave = $this->getColumnValue('salary_leave') ?? 0;
            $payroll->salary_advance = $this->getColumnValue('salary_advance') ?? 0;
            $payroll->salary_hra_advance = $this->getColumnValue('salary_hra_advance') ?? 0;
            $payroll->salary_ot = $this->getColumnValue('salary_ot') ?? 0;
            $payroll->total_deduction = $this->getColumnValue('total_deduction') ?? 0;

            // Overtime details
            $payroll->ot1_hrs = $this->getColumnValue('ot1_hrs') ?? 0;
            $payroll->ot1_rate = $this->getColumnValue('ot1_rate') ?? 0;
            $payroll->ot1_amt = $this->getColumnValue('ot1_amt') ?? 0;
            $payroll->ot2_hrs = $this->getColumnValue('ot2_hrs') ?? 0;
            $payroll->ot2_rate = $this->getColumnValue('ot2_rate') ?? 0;
            $payroll->ot2_amt = $this->getColumnValue('ot2_amt') ?? 0;
            $payroll->ot_total_hrs = $this->getColumnValue('ot_total_hrs') ?? 0;
            $payroll->ot_total_amt = $this->getColumnValue('ot_total_amt') ?? 0;

            $payroll->days_worked = $this->getColumnValue('days_worked') ?? 0;
            $payroll->remarks = $this->getColumnValue('remarks') ?? '';
            $payroll->comments = $this->getColumnValue('comments') ?? '';
            $payroll->added_by = $this->getColumnValue('added_by') ?? 0;
            $payroll->updated_by = $this->getColumnValue('updated_by') ?? 0;


            $payroll->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Payroll import failed: " . $e->getMessage());
        }
    }
}
