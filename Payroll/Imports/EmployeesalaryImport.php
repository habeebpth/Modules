<?php

namespace Modules\Payroll\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class EmployeesalaryImport implements ToArray
{
    public static function fields(): array
    {
        return array(
            ['id' => 'employee_id', 'name' => __('app.employeeId'), 'required' => 'Yes'],
            ['id' => 'basic_salary', 'name' => __('app.BasicSalary'), 'required' => 'Yes'],
            ['id' => 'hra', 'name' => __('app.hra'), 'required' => 'No'],
            ['id' => 'Special', 'name' => __('app.Special'), 'required' => 'No'],
            ['id' => 'Incentive', 'name' => __('app.Incentive'), 'required' => 'No'],

        );
    }

    public function array(array $array): array
    {
        return $array;
    }
}
