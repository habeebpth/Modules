<?php

namespace Modules\Payroll\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class PayrollcalculationImport implements ToArray
{
    public static function fields(): array
    {
        return array(
            ['id' => 'employee', 'name' => __('app.employee'), 'required' => 'Yes'],
            ['id' => 'employee_type', 'name' => __('app.employee_type'), 'required' => 'No'],
            ['id' => 'employee_grade', 'name' => __('app.employee_grade'), 'required' => 'No'],
            ['id' => 'month', 'name' => __('app.month'), 'required' => 'Yes'],
            ['id' => 'year', 'name' => __('app.year'), 'required' => 'Yes'],
            ['id' => 'no_of_days_in_month', 'name' => __('app.no_of_days_in_month'), 'required' => 'No'],
            ['id' => 'no_of_months_in_year', 'name' => __('app.no_of_months_in_year'), 'required' => 'No'],
            ['id' => 'sl_full_pay', 'name' => __('app.sl_full_pay'), 'required' => 'No'],
            ['id' => 'sl_half_pay', 'name' => __('app.sl_half_pay'), 'required' => 'No'],
            ['id' => 'taken_leave', 'name' => __('app.taken_leave'), 'required' => 'No'],
            ['id' => 'absent', 'name' => __('app.absent'), 'required' => 'No'],
            ['id' => 'combo_offs', 'name' => __('app.combo_offs'), 'required' => 'No'],
            ['id' => 'total_leave_earned', 'name' => __('app.total_leave_earned'), 'required' => 'No'],
            ['id' => 'opening_leave_balance', 'name' => __('app.opening_leave_balance'), 'required' => 'No'],
            ['id' => 'closing_leave_balance', 'name' => __('app.closing_leave_balance'), 'required' => 'No'],
            ['id' => 'opening_excess_leave', 'name' => __('app.opening_excess_leave'), 'required' => 'No'],
            ['id' => 'closing_excess_leave', 'name' => __('app.closing_excess_leave'), 'required' => 'No'],
            ['id' => 'excess_leave_taken', 'name' => __('app.excess_leave_taken'), 'required' => 'No'],
            ['id' => 'salary_basic', 'name' => __('app.salary_basic'), 'required' => 'No'],
            ['id' => 'salary_spay', 'name' => __('app.salary_spay'), 'required' => 'No'],
            ['id' => 'salary_hra', 'name' => __('app.salary_hra'), 'required' => 'No'],
            ['id' => 'salary_incentive', 'name' => __('app.salary_incentive'), 'required' => 'No'],
            ['id' => 'salary_gross', 'name' => __('app.salary_gross'), 'required' => 'No'],
            ['id' => 'salary_net', 'name' => __('app.salary_net'), 'required' => 'No'],
            ['id' => 'salary_leave', 'name' => __('app.salary_leave'), 'required' => 'No'],
            ['id' => 'salary_advance', 'name' => __('app.salary_advance'), 'required' => 'No'],
            ['id' => 'salary_hra_advance', 'name' => __('app.salary_hra_advance'), 'required' => 'No'],
            ['id' => 'salary_ot', 'name' => __('app.salary_ot'), 'required' => 'No'],
            ['id' => 'total_deduction', 'name' => __('app.total_deduction'), 'required' => 'No'],
            ['id' => 'ot1_hrs', 'name' => __('app.ot1_hrs'), 'required' => 'No'],
            ['id' => 'ot1_rate', 'name' => __('app.ot1_rate'), 'required' => 'No'],
            ['id' => 'ot1_amt', 'name' => __('app.ot1_amt'), 'required' => 'No'],
            ['id' => 'ot2_hrs', 'name' => __('app.ot2_hrs'), 'required' => 'No'],
            ['id' => 'ot2_rate', 'name' => __('app.ot2_rate'), 'required' => 'No'],
            ['id' => 'ot2_amt', 'name' => __('app.ot2_amt'), 'required' => 'No'],
            ['id' => 'ot_total_hrs', 'name' => __('app.ot_total_hrs'), 'required' => 'No'],
            ['id' => 'ot_total_amt', 'name' => __('app.ot_total_amt'), 'required' => 'No'],
            ['id' => 'days_worked', 'name' => __('app.days_worked'), 'required' => 'No'],
            ['id' => 'remarks', 'name' => __('app.remarks'), 'required' => 'No'],
            ['id' => 'comments', 'name' => __('app.comments'), 'required' => 'No'],
            ['id' => 'added_by', 'name' => __('app.added_by'), 'required' => 'No'],
            ['id' => 'updated_by', 'name' => __('app.updated_by'), 'required' => 'No'],
        );
    }

    public function array(array $array): array
    {
        return $array;
    }
}
