<?php

namespace Modules\Payroll\Entities;

use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Database\factories\PayrollLeaveSalaryCalculationFactory;

class PayrollLeaveSalaryCalculation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sl_full_pay',
        'sl_half_pay',
        'taken_leave',
        'absent',
        'combo_offs',
        'total_leave_earned',
        'opening_leave_balance',
        'closing_leave_balance',
        'opening_excess_leave',
        'closing_excess_leave',
        'excess_leave_taken',
        'salary_basic',
        'salary_spay',
        'salary_hra',
        'salary_incentive',
        'salary_gross',
        'salary_net',
        'salary_leave',
        'salary_advance',
        'salary_hra_advance',
        'salary_ot',
        'total_deduction'
    ];

    protected static function newFactory(): PayrollLeaveSalaryCalculationFactory
    {
        //return PayrollLeaveSalaryCalculationFactory::new();
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id')->withoutGlobalScope(ActiveScope::class);
    }
}
