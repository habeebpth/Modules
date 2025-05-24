<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DwcBillingCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billingCodes = ['B', 'C', 'D', 'EK', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'S'];

        foreach ($billingCodes as $index => $code) {
            DB::table('dwc_billing_codes')->insert([
                'company_id' => 1, // Adjust the company_id if necessary
                'name' => $code,
                'position' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
