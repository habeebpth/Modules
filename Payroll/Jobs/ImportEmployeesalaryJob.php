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
use App\Models\EmployeeDetails;
use Modules\Payroll\Entities\EmployeeMonthlySalary;
use App\Models\User;
use Modules\Payroll\Entities\PayrollLeaveSalaryCalculation;
use Exception;

class ImportEmployeesalaryJob implements ShouldQueue, ShouldBeUnique
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

    public function __construct(array $row, array $columns, $company = null)
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

            $employeeId = $this->getColumnValue('employee_id');
            if (!$employeeId) {
                Log::warning("Missing employee_id in row.");
                DB::rollBack();
                return;
            }

            $employee = EmployeeDetails::where('company_id', $companyId)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$employee) {
                Log::error("Invalid employee ID: $employeeId");
                DB::rollBack();
                return;
            }

            // Basic salary components
            $basicSalary = (float) $this->getColumnValue('basic_salary');
            $hra = (float) $this->getColumnValue('hra');
            $special = (float) $this->getColumnValue('Special');
            $incentive = (float) $this->getColumnValue('Incentive');

            $amount = $basicSalary + $hra + $special + $incentive;
            $annualSalary = $amount * 12;

            $monthlySalary = new EmployeeMonthlySalary();
            $monthlySalary->company_id = $companyId;
            $monthlySalary->user_id = $employee->id;
            $monthlySalary->amount = $amount;
            $monthlySalary->annual_salary = $annualSalary;
            $monthlySalary->basic_salary = $basicSalary;
            $monthlySalary->fixed_allowance = 0;
            $monthlySalary->basic_value_type = 'fixed';
            $monthlySalary->type = 'initial';
            $monthlySalary->date = now();
            $monthlySalary->save();

            // Handle variable components
            $rowData = $this->row ?? [];

dd($rowData);
            foreach ($rowData as $column => $value) {
                if (in_array($column, ['employee_id', 'basic_salary'])) {
                    continue;
                }

                $component = DB::table('salary_components')
                    ->where('company_id', $companyId)
                    ->where('component_name', $column)
                    ->first();
                    dd($component);
                if ($component) {
                    DB::table('employee_variable_salaries')->insert([
                        'monthly_salary_id' => $monthlySalary->id,
                        'variable_component_id' => $component->id,
                        'variable_value' => (float) $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Payroll import failed: " . $e->getMessage());
        }
    }
}
