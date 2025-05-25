<?php
namespace Modules\Accounting\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\Accounting\Entities\Journal;

class JournalPosted
{
    use Dispatchable;

    public $journal;

    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }
}
