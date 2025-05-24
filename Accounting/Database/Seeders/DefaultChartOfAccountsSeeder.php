<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\Entities\ChartOfAccount;

class DefaultChartOfAccountsSeeder extends Seeder
{
    public function run()
    {
        $companyId = 1; // Default company or pass as parameter

        $accounts = [
            // ASSETS
            ['code' => '1000', 'name' => 'ASSETS', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => null],
            ['code' => '1100', 'name' => 'Current Assets', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1000'],
            ['code' => '1110', 'name' => 'Cash and Cash Equivalents', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1100'],
            ['code' => '1111', 'name' => 'Petty Cash', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1110'],
            ['code' => '1112', 'name' => 'Cash in Bank', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1110'],
            ['code' => '1120', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1100'],
            ['code' => '1130', 'name' => 'Inventory', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1100'],
            ['code' => '1140', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'sub_type' => 'current_asset', 'parent' => '1100'],

            ['code' => '1200', 'name' => 'Fixed Assets', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1000'],
            ['code' => '1210', 'name' => 'Property, Plant & Equipment', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1200'],
            ['code' => '1211', 'name' => 'Buildings', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1210'],
            ['code' => '1212', 'name' => 'Equipment', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1210'],
            ['code' => '1213', 'name' => 'Vehicles', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1210'],
            ['code' => '1220', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'parent' => '1200'],

            // LIABILITIES
            ['code' => '2000', 'name' => 'LIABILITIES', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => null],
            ['code' => '2100', 'name' => 'Current Liabilities', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => '2000'],
            ['code' => '2110', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => '2100'],
            ['code' => '2120', 'name' => 'Accrued Expenses', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => '2100'],
            ['code' => '2130', 'name' => 'Short-term Loans', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => '2100'],
            ['code' => '2140', 'name' => 'Tax Payable', 'type' => 'liability', 'sub_type' => 'current_liability', 'parent' => '2100'],

            ['code' => '2200', 'name' => 'Long-term Liabilities', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'parent' => '2000'],
            ['code' => '2210', 'name' => 'Long-term Loans', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'parent' => '2200'],
            ['code' => '2220', 'name' => 'Mortgage Payable', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'parent' => '2200'],

            // EQUITY
            ['code' => '3000', 'name' => 'EQUITY', 'type' => 'equity', 'sub_type' => 'owners_equity', 'parent' => null],
            ['code' => '3100', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'sub_type' => 'owners_equity', 'parent' => '3000'],
            ['code' => '3110', 'name' => 'Share Capital', 'type' => 'equity', 'sub_type' => 'owners_equity', 'parent' => '3100'],
            ['code' => '3120', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'retained_earnings', 'parent' => '3100'],
            ['code' => '3130', 'name' => 'Current Year Earnings', 'type' => 'equity', 'sub_type' => 'retained_earnings', 'parent' => '3100'],

            // REVENUE
            ['code' => '4000', 'name' => 'REVENUE', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'parent' => null],
            ['code' => '4100', 'name' => 'Operating Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'parent' => '4000'],
            ['code' => '4110', 'name' => 'Sales Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'parent' => '4100'],
            ['code' => '4120', 'name' => 'Service Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'parent' => '4100'],
            ['code' => '4130', 'name' => 'Hotel Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'parent' => '4100'],

            ['code' => '4200', 'name' => 'Other Revenue', 'type' => 'revenue', 'sub_type' => 'other_revenue', 'parent' => '4000'],
            ['code' => '4210', 'name' => 'Interest Income', 'type' => 'revenue', 'sub_type' => 'other_revenue', 'parent' => '4200'],
            ['code' => '4220', 'name' => 'Other Income', 'type' => 'revenue', 'sub_type' => 'other_revenue', 'parent' => '4200'],

            // EXPENSES
            ['code' => '5000', 'name' => 'EXPENSES', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => null],
            ['code' => '5100', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5000'],
            ['code' => '5110', 'name' => 'Materials', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5100'],
            ['code' => '5120', 'name' => 'Direct Labor', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5100'],

            ['code' => '5200', 'name' => 'Operating Expenses', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5000'],
            ['code' => '5210', 'name' => 'Salaries and Wages', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5220', 'name' => 'Rent Expense', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5230', 'name' => 'Utilities Expense', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5240', 'name' => 'Office Supplies', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5250', 'name' => 'Marketing Expense', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5260', 'name' => 'Travel Expense', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],
            ['code' => '5270', 'name' => 'Depreciation Expense', 'type' => 'expense', 'sub_type' => 'operating_expense', 'parent' => '5200'],

            ['code' => '5300', 'name' => 'Other Expenses', 'type' => 'expense', 'sub_type' => 'other_expense', 'parent' => '5000'],
            ['code' => '5310', 'name' => 'Interest Expense', 'type' => 'expense', 'sub_type' => 'other_expense', 'parent' => '5300'],
            ['code' => '5320', 'name' => 'Bank Charges', 'type' => 'expense', 'sub_type' => 'other_expense', 'parent' => '5300'],
        ];

        foreach ($accounts as $account) {
            $parentId = null;
            if ($account['parent']) {
                $parent = ChartOfAccount::where('account_code', $account['parent'])
                    ->where('company_id', $companyId)
                    ->first();
                $parentId = $parent ? $parent->id : null;
            }

            ChartOfAccount::create([
                'company_id' => $companyId,
                'parent_id' => $parentId,
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'account_type' => $account['type'],
                'account_sub_type' => $account['sub_type'],
                'is_active' => true,
                'is_default' => true,
                'opening_balance' => 0,
                'current_balance' => 0,
            ]);
        }
    }
}