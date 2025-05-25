<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconciliation extends Model
{
    protected $fillable = [
        'company_id',
        'account_id',
        'reconciliation_date',
        'statement_balance',
        'book_balance',
        'difference',
        'status',
        'notes',
        'created_by',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'statement_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'reviewed_at' => 'datetime'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
