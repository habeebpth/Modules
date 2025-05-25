<?php
namespace Modules\Accounting\Rules;

use Illuminate\Contracts\Validation\Rule;

class BalancedJournalRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        $totalDebit = collect($value)->sum('debit');
        $totalCredit = collect($value)->sum('credit');

        return abs($totalDebit - $totalCredit) < 0.01; // Allow for rounding differences
    }

    public function message()
    {
        return 'The journal entries must be balanced (total debits must equal total credits).';
    }
}
