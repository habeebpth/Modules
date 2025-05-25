<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\Budget;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\Journal;
use Carbon\Carbon;

class BudgetService
{
    public function updateActualAmount($budgetId)
    {
        $budget = Budget::findOrFail($budgetId);

        // Calculate date range based on period
        $fiscalYear = $budget->fiscalYear;
        $startDate = $this->getPeriodStartDate($fiscalYear, $budget->period_type, $budget->period_number);
        $endDate = $this->getPeriodEndDate($fiscalYear, $budget->period_type, $budget->period_number);

        // Calculate actual amount from journal entries
        $actualAmount = JournalEntry::where('account_id', $budget->account_id)
            ->whereHas('journal', function($query) use ($startDate, $endDate) {
                $query->where('status', Journal::STATUS_POSTED)
                      ->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('debit') - JournalEntry::where('account_id', $budget->account_id)
            ->whereHas('journal', function($query) use ($startDate, $endDate) {
                $query->where('status', Journal::STATUS_POSTED)
                      ->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('credit');

        // Update budget
        $budget->update([
            'actual_amount' => $actualAmount,
            'variance' => $budget->budgeted_amount - $actualAmount
        ]);

        return $budget;
    }

    public function getBudgetVarianceReport($fiscalYearId, $periodType = null)
    {
        $query = Budget::where('company_id', user()->company_id)
            ->where('fiscal_year_id', $fiscalYearId)
            ->with(['account', 'fiscalYear']);

        if ($periodType) {
            $query->where('period_type', $periodType);
        }

        return $query->get()->map(function($budget) {
            return [
                'account' => $budget->account,
                'period' => $budget->period_type . ' ' . $budget->period_number,
                'budgeted' => $budget->budgeted_amount,
                'actual' => $budget->actual_amount,
                'variance' => $budget->variance,
                'variance_percent' => $budget->budgeted_amount > 0 ?
                    ($budget->variance / $budget->budgeted_amount) * 100 : 0
            ];
        });
    }

    private function getPeriodStartDate($fiscalYear, $periodType, $periodNumber)
    {
        $start = Carbon::parse($fiscalYear->start_date);

        switch($periodType) {
            case 'monthly':
                return $start->addMonths($periodNumber - 1)->startOfMonth();
            case 'quarterly':
                return $start->addQuarters($periodNumber - 1)->startOfQuarter();
            case 'yearly':
                return $start;
        }
    }

    private function getPeriodEndDate($fiscalYear, $periodType, $periodNumber)
    {
        $start = Carbon::parse($fiscalYear->start_date);

        switch($periodType) {
            case 'monthly':
                return $start->addMonths($periodNumber - 1)->endOfMonth();
            case 'quarterly':
                return $start->addQuarters($periodNumber - 1)->endOfQuarter();
            case 'yearly':
                return Carbon::parse($fiscalYear->end_date);
        }
    }
}
