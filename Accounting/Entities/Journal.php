<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Journal extends Model
{
    protected $fillable = [
        'company_id', 'journal_number', 'date', 'description',
        'reference_type', 'reference_id', 'total_debit', 'total_credit',
        'status', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2'
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_POSTED = 'posted';
    const STATUS_REVERSED = 'reversed';

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function isBalanced()
    {
        return $this->total_debit == $this->total_credit;
    }
}
