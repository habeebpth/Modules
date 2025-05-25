<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'company_id', 'fiscal_year_id', 'account_id', 'period_type', 'period_number',
        'budgeted_amount', 'actual_amount', 'variance', 'notes'
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2'
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
