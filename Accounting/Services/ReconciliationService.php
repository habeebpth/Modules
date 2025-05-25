<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\Reconciliation;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\Journal;
use Carbon\Carbon;

class ReconciliationService
{
    public function createReconciliation($accountId, $reconciliationDate, $statementBalance)
    {
        $account = ChartOfAccount::findOrFail($accountId);

        // Calculate book balance as of reconciliation date
        $bookBalance = $this->calculateBookBalance($accountId, $reconciliationDate);

        $reconciliation = Reconciliation::create([
            'company_id' => user()->company_id,
            'account_id' => $accountId,
            'reconciliation_date' => $reconciliationDate,
            'statement_balance' => $statementBalance,
            'book_balance' => $bookBalance,
            'difference' => $statementBalance - $bookBalance,
            'status' => 'draft',
            'created_by' => user()->id
        ]);

        return $reconciliation;
    }

    public function calculateBookBalance($accountId, $asOfDate)
    {
        $debitTotal = JournalEntry::where('account_id', $accountId)
            ->whereHas('journal', function($query) use ($asOfDate) {
                $query->where('status', Journal::STATUS_POSTED)
                      ->where('date', '<=', $asOfDate);
            })
            ->sum('debit');

        $creditTotal = JournalEntry::where('account_id', $accountId)
            ->whereHas('journal', function($query) use ($asOfDate) {
                $query->where('status', Journal::STATUS_POSTED)
                      ->where('date', '<=', $asOfDate);
            })
            ->sum('credit');

        return $debitTotal - $creditTotal;
    }

    public function getUnreconciledTransactions($accountId, $reconciliationDate)
    {
        return JournalEntry::where('account_id', $accountId)
            ->whereHas('journal', function($query) use ($reconciliationDate) {
                $query->where('status', Journal::STATUS_POSTED)
                      ->where('date', '<=', $reconciliationDate);
            })
            ->with(['journal'])
            ->get();
    }
}
