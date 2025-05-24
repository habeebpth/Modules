<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Database\factories\EmployeeExpenseFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EmployeeExpense extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): EmployeeExpenseFactory
    {
        //return EmployeeExpenseFactory::new();
    }
    public function employeeuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id')->withoutGlobalScope(ActiveScope::class);
    }
    public function installments()
    {
        return $this->hasMany(PayrollEmployeeExpenseRepayment::class, 'employee_expense_id');
    }
}
