<?php

namespace Modules\Payroll\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Payroll\Entities\EmployeeMonthlySalary;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payroll\Entities\EmployeeVariableComponent;

class EmployeeSalaryImports implements ToCollection, WithHeadingRow
{
    protected $company;

    public function __construct($company)
    {
        $this->company = $company;
    }

    public function collection(Collection $rows)
{
    DB::beginTransaction();

    try {
        $companyId = $this->company?->id;

        if (!$companyId) {
            Log::error("Company ID not found for payroll import.");
            DB::rollBack();
            return;
        }

        foreach ($rows as $row) {
            $employeeId = $row['employee_id'] ?? null;

            if (!$employeeId) {
                Log::warning("Missing employee_id in row.");
                continue;
            }

            $employee = EmployeeDetails::where('company_id', $companyId)
                ->where('employee_id', $employeeId)
                ->first();

            if (!$employee) {
                Log::error("Invalid employee ID: $employeeId");
                continue;
            }

            // Basic components
            $basicSalary = (float) ($row['basic_salary'] ?? 0);
            $hra         = (float) ($row['hra'] ?? 0);
            $special     = (float) ($row['special'] ?? 0);
            $incentive   = (float) ($row['incentive'] ?? 0);

            $amount = $basicSalary + $hra + $special + $incentive;
            $annualSalary = $amount * 12;

            $monthlySalary = EmployeeMonthlySalary::where('user_id', $employee->id)
                ->where('type', 'initial')
                ->first();

            if ($monthlySalary) {
                // Update existing salary
                $monthlySalary->amount = $amount;
                $monthlySalary->annual_salary = $annualSalary;
                $monthlySalary->effective_annual_salary = $annualSalary;
                $monthlySalary->effective_monthly_salary = $amount;
                $monthlySalary->basic_salary = $basicSalary;
                $monthlySalary->fixed_allowance = 0;
                $monthlySalary->basic_value_type = 'fixed';
                $monthlySalary->date = now()->timezone($this->company->timezone)->toDateString();
                $monthlySalary->save();

                // Remove previous variable components
                EmployeeVariableComponent::where('monthly_salary_id', $monthlySalary->id)->delete();
            } else {
                // Create new salary
                $monthlySalary = new EmployeeMonthlySalary();
                $monthlySalary->company_id = $companyId;
                $monthlySalary->user_id = $employee->id;
                $monthlySalary->amount = $amount;
                $monthlySalary->annual_salary = $annualSalary;
                $monthlySalary->effective_annual_salary = $annualSalary;
                $monthlySalary->effective_monthly_salary = $amount;
                $monthlySalary->basic_salary = $basicSalary;
                $monthlySalary->fixed_allowance = 0;
                $monthlySalary->basic_value_type = 'fixed';
                $monthlySalary->type = 'initial';
                $monthlySalary->date = now()->timezone($this->company->timezone)->toDateString();
                $monthlySalary->save();
            }

            // Handle variable components
            foreach ($row as $column => $value) {
                if (in_array($column, ['employee_id', 'basic_salary'])) {
                    continue;
                }

                $component = DB::table('salary_components')
                    ->where('company_id', $companyId)
                    ->where('component_name', $column)
                    ->first();

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

            // Ensure payroll cycle exists
            $employeeCycle = \Modules\Payroll\Entities\EmployeePayrollCycle::where('user_id', $employee->id)->first();

            if (is_null($employeeCycle)) {
                $payrollCycle = \Modules\Payroll\Entities\PayrollCycle::where('cycle', 'monthly')->first();

                if ($payrollCycle) {
                    $employeeCycle = new \Modules\Payroll\Entities\EmployeePayrollCycle();
                    $employeeCycle->user_id = $employee->id;
                    $employeeCycle->payroll_cycle_id = $payrollCycle->id;
                    $employeeCycle->save();
                }
            }
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Payroll import failed: " . $e->getMessage());
    }
}

}
