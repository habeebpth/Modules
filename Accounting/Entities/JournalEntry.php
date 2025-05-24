<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'company_id', 'journal_id', 'account_id', 'debit', 'credit',
        'description', 'reference_type', 'reference_id'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
