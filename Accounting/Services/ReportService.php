<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\Journal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getTrialBalance($asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now();
        
        $accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('is_active', true)
            ->with(['journalEntries' => function($query) use ($asOfDate) {
                $query->whereHas('journal', function($q) use ($asOfDate) {
                    $q->where('status', Journal::STATUS_POSTED)
                      ->where('date', '<=', $asOfDate);
                });
            }])
            ->orderBy('account_code')
            ->get();

        $trialBalance = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $debitTotal = $account->journalEntries->sum('debit');
            $creditTotal = $account->journalEntries->sum('credit');
            
            if ($debitTotal > 0 || $creditTotal > 0) {
                $balance = $debitTotal - $creditTotal;
                
                $trialBalance[] = [
                    'account' => $account,
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal,
                    'debit_balance' => $balance > 0 ? $balance : 0,
                    'credit_balance' => $balance < 0 ? abs($balance) : 0,
                ];
                
                $totalDebits += $balance > 0 ? $balance : 0;
                $totalCredits += $balance < 0 ? abs($balance) : 0;
            }
        }

        return [
            'accounts' => $trialBalance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'as_of_date' => $asOfDate,
        ];
    }

    public function getBalanceSheet($asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now();
        
        $assets = $this->getAccountTypeBalances('asset', $asOfDate);
        $liabilities = $this->getAccountTypeBalances('liability', $asOfDate);
        $equity = $this->getAccountTypeBalances('equity', $asOfDate);
        
        $totalAssets = collect($assets)->sum('balance');
        $totalLiabilities = collect($liabilities)->sum('balance');
        $totalEquity = collect($equity)->sum('balance');
        
        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
            'as_of_date' => $asOfDate,
        ];
    }

    public function getIncomeStatement($fromDate, $toDate)
    {
        $revenue = $this->getAccountTypeBalances('revenue', $toDate, $fromDate);
        $expenses = $this->getAccountTypeBalances('expense', $toDate, $fromDate);
        
        $totalRevenue = collect($revenue)->sum('balance');
        $totalExpenses = collect($expenses)->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;
        
        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];
    }

    public function getGeneralLedger($accountId, $fromDate, $toDate)
    {
        $account = ChartOfAccount::findOrFail($accountId);
        
        // Get opening balance
        $openingBalance = JournalEntry::where('account_id', $accountId)
            ->whereHas('journal', function($q) use ($fromDate) {
                $q->where('status', Journal::STATUS_POSTED)
                  ->where('date', '<', $fromDate);
            })
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance') ?? 0;

        // Get entries for the period
        $entries = JournalEntry::with(['journal', 'account'])
            ->where('account_id', $accountId)
            ->whereHas('journal', function($q) use ($fromDate, $toDate) {
                $q->where('status', Journal::STATUS_POSTED)
                  ->whereBetween('date', [$fromDate, $toDate]);
            })
            ->orderBy('created_at')
            ->get();

        $runningBalance = $openingBalance;
        $ledgerEntries = [];

        foreach ($entries as $entry) {
            $runningBalance += ($entry->debit - $entry->credit);
            
            $ledgerEntries[] = [
                'date' => $entry->journal->date,
                'journal_number' => $entry->journal->journal_number,
                'description' => $entry->description ?: $entry->journal->description,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
                'balance' => $runningBalance,
            ];
        }

        return [
            'account' => $account,
            'opening_balance' => $openingBalance,
            'entries' => $ledgerEntries,
            'closing_balance' => $runningBalance,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];
    }

    protected function getAccountTypeBalances($accountType, $asOfDate, $fromDate = null)
    {
        $query = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', $accountType)
            ->where('is_active', true)
            ->with(['journalEntries' => function($q) use ($asOfDate, $fromDate) {
                $q->whereHas('journal', function($query) use ($asOfDate, $fromDate) {
                    $query->where('status', Journal::STATUS_POSTED)
                          ->where('date', '<=', $asOfDate);
                    if ($fromDate) {
                        $query->where('date', '>=', $fromDate);
                    }
                });
            }])
            ->orderBy('account_code');

        $accounts = $query->get();
        $accountBalances = [];

        foreach ($accounts as $account) {
            $debitTotal = $account->journalEntries->sum('debit');
            $creditTotal = $account->journalEntries->sum('credit');
            
            // Calculate balance based on account type
            if (in_array($accountType, ['asset', 'expense'])) {
                $balance = $debitTotal - $creditTotal; // Normal debit balance
            } else {
                $balance = $creditTotal - $debitTotal; // Normal credit balance
            }
            
            if ($balance != 0) {
                $accountBalances[] = [
                    'account' => $account,
                    'balance' => $balance,
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal,
                ];
            }
        }

        return $accountBalances;
    }
}