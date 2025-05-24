<?php

namespace Modules\Payroll\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CustomFieldsTrait;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasCompany;
use Illuminate\Notifications\Notifiable;
use Modules\Payroll\Database\factories\PayrollSalaryAdvanceFactory;

class PayrollSalaryAdvance extends Model
{
    use Notifiable;
    use HasFactory;
    use CustomFieldsTrait;
    use HasCompany;
    public $table = 'payroll_salary_advance';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'advance_type',
        'employee_id',
        'request_date',
        'amount',
        'reason',
        'approval_status',
        'approved_by',
        'added_by',
        'approval_date',
        'disbursement_date',
        'transaction_reference',
        'payment_mode',
        'repayment_method',
        'repayment_status',
    ];
    protected static function newFactory(): PayrollSalaryAdvanceFactory
    {
        //return PayrollSalaryAdvanceFactory::new();
    }

    public function employeeuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id')->withoutGlobalScope(ActiveScope::class);
    }
    public function installments()
    {
        return $this->hasMany(PayrollSalaryAdvanceRepayment::class, 'salary_advance_id');
    }

}
