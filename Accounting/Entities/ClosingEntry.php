<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosingEntry extends Model
{
    protected $fillable = [
        'company_id', 'fiscal_year_id', 'journal_id', 'type', 'closing_date', 'amount', 'description'
    ];

    protected $casts = [
        'closing_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
