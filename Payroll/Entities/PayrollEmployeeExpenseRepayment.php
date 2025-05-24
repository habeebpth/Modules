<?php

namespace Modules\Payroll\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Database\factories\PayrollEmployeeExpenseRepaymentFactory;

class PayrollEmployeeExpenseRepayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PayrollEmployeeExpenseRepaymentFactory
    {
        //return PayrollEmployeeExpenseRepaymentFactory::new();
    }
    public function files(): HasMany
    {
        return $this->hasMany(PayrollFiles::class, 'employee_expense_repayment_id')->orderByDesc('id');
    }
    public function addedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by')->withoutGlobalScope(ActiveScope::class);
    }
    public function lastupdatedby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by')->withoutGlobalScope(ActiveScope::class);
    }
}
