<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\ClosingEntry;
use Modules\Accounting\Entities\FiscalYear;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\Journal;
use Modules\Accounting\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class ClosingService
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function performYearEndClose($fiscalYearId)
    {
        return DB::transaction(function() use ($fiscalYearId) {
            $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

            if ($fiscalYear->is_closed) {
                throw new \Exception('Fiscal year is already closed');
            }

            // Get retained earnings account
            $retainedEarningsAccount = ChartOfAccount::where('company_id', user()->company_id)
                ->where('account_sub_type', 'retained_earnings')
                ->first();

            if (!$retainedEarningsAccount) {
                throw new \Exception('Retained earnings account not found');
            }

            // Close revenue accounts
            $this->closeRevenueAccounts($fiscalYear, $retainedEarningsAccount);

            // Close expense accounts
            $this->closeExpenseAccounts($fiscalYear, $retainedEarningsAccount);

            // Mark fiscal year as closed
            $fiscalYear->update(['is_closed' => true]);

            return true;
        });
    }

    private function closeRevenueAccounts($fiscalYear, $retainedEarningsAccount)
    {
        $revenueAccounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', 'revenue')
            ->get();

        foreach ($revenueAccounts as $account) {
            $balance = $this->getAccountBalance($account->id, $fiscalYear->end_date);

            if ($balance != 0) {
                $entries = [
                    [
                        'account_id' => $account->id,
                        'debit' => $balance, // Close revenue with debit
                        'credit' => 0,
                        'description' => 'Year end closing - Revenue'
                    ],
                    [
                        'account_id' => $retainedEarningsAccount->id,
                        'debit' => 0,
                        'credit' => $balance,
                        'description' => 'Year end closing - Revenue'
                    ]
                ];

                $journal = $this->accountingService->createJournalEntry(
                    $entries,
                    'Year end closing - Revenue accounts',
                    'closing_entry',
                    $fiscalYear->id,
                    $fiscalYear->end_date
                );

                ClosingEntry::create([
                    'company_id' => user()->company_id,
                    'fiscal_year_id' => $fiscalYear->id,
                    'journal_id' => $journal->id,
                    'type' => 'revenue',
                    'closing_date' => $fiscalYear->end_date,
                    'amount' => $balance,
                    'description' => 'Close revenue account: ' . $account->account_name
                ]);
            }
        }
    }

    private function closeExpenseAccounts($fiscalYear, $retainedEarningsAccount)
    {
        $expenseAccounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', 'expense')
            ->get();

        foreach ($expenseAccounts as $account) {
            $balance = $this->getAccountBalance($account->id, $fiscalYear->end_date);

            if ($balance != 0) {
                $entries = [
                    [
                        'account_id' => $retainedEarningsAccount->id,
                        'debit' => $balance,
                        'credit' => 0,
                        'description' => 'Year end closing - Expenses'
                    ],
                    [
                        'account_id' => $account->id,
                        'debit' => 0,
                        'credit' => $balance, // Close expense with credit
                        'description' => 'Year end closing - Expenses'
                    ]
                ];

                $journal = $this->accountingService->createJournalEntry(
                    $entries,
                    'Year end closing - Expense accounts',
                    'closing_entry',
                    $fiscalYear->id,
                    $fiscalYear->end_date
                );

                ClosingEntry::create([
                    'company_id' => user()->company_id,
                    'fiscal_year_id' => $fiscalYear->id,
                    'journal_id' => $journal->id,
                    'type' => 'expense',
                    'closing_date' => $fiscalYear->end_date,
                    'amount' => $balance,
                    'description' => 'Close expense account: ' . $account->account_name
                ]);
            }
        }
    }

    private function getAccountBalance($accountId, $asOfDate)
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
}
