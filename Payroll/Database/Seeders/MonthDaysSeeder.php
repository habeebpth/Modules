<?php

namespace Modules\Payroll\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Payroll\Entities\MonthDay;

class MonthDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $months = [
            'January' => 31, 'February' => 28, 'March' => 31, 'April' => 30,
            'May' => 31, 'June' => 30, 'July' => 31, 'August' => 31,
            'September' => 30, 'October' => 31, 'November' => 30, 'December' => 31
        ];

        // Adjust for leap year
        $currentYear = now()->year;
        if ($currentYear % 4 === 0 && ($currentYear % 100 !== 0 || $currentYear % 400 === 0)) {
            $months['February'] = 29;
        }

        foreach ($months as $month => $days) {
            MonthDay::updateOrCreate(
                ['month' => $month],
                ['days_in_month' => $days]
            );
        }

    }
}
