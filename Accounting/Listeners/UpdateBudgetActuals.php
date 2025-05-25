<?php
// =======================
// FILE: Accounting/Listeners/UpdateBudgetActuals.php
// =======================

namespace Modules\Accounting\Listeners;

use Modules\Accounting\Events\JournalPosted;
use Modules\Accounting\Services\BudgetService;
use Modules\Accounting\Entities\Budget;

class UpdateBudgetActuals
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function handle(JournalPosted $event)
    {
        // Update budget actual amounts when journal is posted
        foreach ($event->journal->entries as $entry) {
            $budgets = Budget::where('account_id', $entry->account_id)->get();

            foreach ($budgets as $budget) {
                $this->budgetService->updateActualAmount($budget->id);
            }
        }
    }
}
