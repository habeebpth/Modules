<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'parent_id', 'account_code', 'account_name',
        'account_type', 'account_sub_type', 'description', 'is_active',
        'is_default', 'opening_balance', 'current_balance'
    ];

    // Account types
    const ACCOUNT_TYPES = [
        'asset' => 'Assets',
        'liability' => 'Liabilities',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expenses'
    ];

    // Account sub-types
    const ACCOUNT_SUB_TYPES = [
        // Assets
        'current_asset' => 'Current Assets',
        'fixed_asset' => 'Fixed Assets',
        'other_asset' => 'Other Assets',

        // Liabilities
        'current_liability' => 'Current Liabilities',
        'long_term_liability' => 'Long Term Liabilities',

        // Equity
        'owners_equity' => 'Owner\'s Equity',
        'retained_earnings' => 'Retained Earnings',

        // Revenue
        'operating_revenue' => 'Operating Revenue',
        'other_revenue' => 'Other Revenue',

        // Expenses
        'operating_expense' => 'Operating Expenses',
        'other_expense' => 'Other Expenses'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'account_id');
    }

    public function getBalanceAttribute()
    {
        return $this->journalEntries()->sum('debit') - $this->journalEntries()->sum('credit');
    }
}
