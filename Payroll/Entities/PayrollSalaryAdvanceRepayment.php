<?php

namespace Modules\Payroll\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Database\factories\PayrollSalaryAdvanceRepaymentFactory;

class PayrollSalaryAdvanceRepayment extends Model
{
    use HasFactory;
    public $table = 'payroll_salary_advance_repayment';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PayrollSalaryAdvanceRepaymentFactory
    {
        //return PayrollSalaryAdvanceRepaymentFactory::new();
    }
    public function files(): HasMany
    {
        return $this->hasMany(PayrollFiles::class, 'advance_repayment_id')->orderByDesc('id');
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
