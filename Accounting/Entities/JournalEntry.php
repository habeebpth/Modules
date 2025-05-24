<?php

namespace Modules\Accounting\Entities;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends BaseModel
{
    use SoftDeletes;

    protected $table = 'journal_entries';

    protected $fillable = [
        'company_id',
        'reference_number',
        'entry_date',
        'description',
        'status',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $dates = [
        'entry_date',
        'posted_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get all the items for this journal entry
     */
    public function items()
    {
        return $this->hasMany(JournalEntryItem::class, 'journal_entry_id');
    }

    /**
     * Get the user who created this journal entry
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who posted this journal entry
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Get the total debits for this journal entry
     */
    public function getTotalDebitAttribute()
    {
        return $this->items()->sum('debit');
    }

    /**
     * Get the total credits for this journal entry
     */
    public function getTotalCreditAttribute()
    {
        return $this->items()->sum('credit');
    }

    /**
     * Check if the journal entry is balanced
     */
    public function getIsBalancedAttribute()
    {
        return $this->total_debit == $this->total_credit;
    }

    /**
     * Check if the journal entry can be posted
     */
    public function canBePosted()
    {
        return $this->status == 'draft' && $this->is_balanced;
    }

    /**
     * Post the journal entry
     */
    public function post($userId)
    {
        if (!$this->canBePosted()) {
            return false;
        }

        $this->status = 'posted';
        $this->posted_by = $userId;
        $this->posted_at = now();
        return $this->save();
    }

    /**
     * Void the journal entry
     */
    public function void()
    {
        if ($this->status != 'posted') {
            return false;
        }

        $this->status = 'voided';
        return $this->save();
    }
}
