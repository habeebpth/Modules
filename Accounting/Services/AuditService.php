<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\Journal;
use Modules\Accounting\Entities\ChartOfAccount;

class AuditService
{
    public function getAccountingAuditTrail($fromDate = null, $toDate = null)
    {
        $query = Journal::where('company_id', user()->company_id)
            ->with(['entries.account'])
            ->orderBy('created_at', 'desc');

        if ($fromDate) {
            $query->where('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('date', '<=', $toDate);
        }

        return $query->get()->map(function($journal) {
            return [
                'date' => $journal->date,
                'journal_number' => $journal->journal_number,
                'description' => $journal->description,
                'status' => $journal->status,
                'total_debit' => $journal->total_debit,
                'total_credit' => $journal->total_credit,
                'created_at' => $journal->created_at,
                'created_by' => $journal->created_by,
                'entries' => $journal->entries->map(function($entry) {
                    return [
                        'account_code' => $entry->account->account_code,
                        'account_name' => $entry->account->account_name,
                        'debit' => $entry->debit,
                        'credit' => $entry->credit,
                        'description' => $entry->description
                    ];
                })
            ];
        });
    }

    public function validateAccountingIntegrity()
    {
        $errors = [];

        // Check for unbalanced journals
        $unbalancedJournals = Journal::where('company_id', user()->company_id)
            ->whereRaw('total_debit != total_credit')
            ->get();

        if ($unbalancedJournals->count() > 0) {
            $errors[] = [
                'type' => 'unbalanced_journals',
                'message' => 'Found ' . $unbalancedJournals->count() . ' unbalanced journal entries',
                'journals' => $unbalancedJournals->pluck('journal_number')
            ];
        }

        // Check for accounts with inconsistent balances
        $accounts = ChartOfAccount::where('company_id', user()->company_id)->get();
        foreach ($accounts as $account) {
            $calculatedBalance = $account->journalEntries()->sum('debit') - $account->journalEntries()->sum('credit');
            if (abs($calculatedBalance - $account->current_balance) > 0.01) { // Allow for rounding differences
                $errors[] = [
                    'type' => 'balance_mismatch',
                    'message' => 'Account balance mismatch for ' . $account->account_name,
                    'account_code' => $account->account_code,
                    'stored_balance' => $account->current_balance,
                    'calculated_balance' => $calculatedBalance
                ];
            }
        }

        return $errors;
    }
}
