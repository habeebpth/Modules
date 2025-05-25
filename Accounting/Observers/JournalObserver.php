<?php
namespace Modules\Accounting\Observers;

use Modules\Accounting\Entities\Journal;
use Modules\Accounting\Events\JournalPosted;

class JournalObserver
{
    public function updated(Journal $journal)
    {
        if ($journal->isDirty('status') && $journal->status === Journal::STATUS_POSTED) {
            event(new JournalPosted($journal));
        }
    }
}
