<?php

namespace Modules\Accounting\Entities;

use App\Models\BaseModel;

class JournalEntryItem extends BaseModel
{
    protected $table = 'journal_entry_items';

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'debit',
        'credit'
    ];

    /**
     * Get the journal entry that this item belongs to
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * Get the account for this journal entry item
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
