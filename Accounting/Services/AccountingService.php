<?php
namespace Modules\Accounting\Services;

use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\Journal;
use Modules\Accounting\Entities\JournalEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingService
{
    /**
     * Create journal entry with automatic balancing validation
     */
    public function createJournalEntry($entries, $description, $referenceType = null, $referenceId = null, $date = null)
    {
        return DB::transaction(function () use ($entries, $description, $referenceType, $referenceId, $date) {
            $totalDebit = collect($entries)->sum('debit');
            $totalCredit = collect($entries)->sum('credit');

            if ($totalDebit != $totalCredit) {
                throw new \Exception('Journal entry is not balanced. Total debits must equal total credits.');
            }

            // Create journal
            $journal = Journal::create([
                'company_id' => user()->company_id,
                'journal_number' => $this->generateJournalNumber(),
                'date' => $date ?? now(),
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => Journal::STATUS_POSTED,
                'created_by' => user()->id
            ]);

            // Create journal entries
            foreach ($entries as $entry) {
                JournalEntry::create([
                    'company_id' => user()->company_id,
                    'journal_id' => $journal->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? $description,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId
                ]);

                // Update account balance
                $this->updateAccountBalance($entry['account_id']);
            }

            return $journal;
        });
    }

    /**
     * Get balance for account type
     */
    public function getAccountTypeBalance($accountType)
    {
        $accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', $accountType)
            ->get();

        $totalBalance = 0;
        foreach ($accounts as $account) {
            $debitTotal = $account->journalEntries()->sum('debit');
            $creditTotal = $account->journalEntries()->sum('credit');

            // For assets and expenses, debit increases balance
            if (in_array($accountType, ['asset', 'expense'])) {
                $totalBalance += ($debitTotal - $creditTotal);
            } else {
                // For liabilities, equity, and revenue, credit increases balance
                $totalBalance += ($creditTotal - $debitTotal);
            }
        }

        return $totalBalance;
    }

    /**
     * Update account current balance - Made public for controller access
     */
    public function updateAccountBalance($accountId)
    {
        $account = ChartOfAccount::find($accountId);
        if (!$account) return;

        $debitTotal = $account->journalEntries()->sum('debit');
        $creditTotal = $account->journalEntries()->sum('credit');

        $balance = $debitTotal - $creditTotal;
        $account->update(['current_balance' => $balance]);
    }

    /**
     * Generate unique journal number
     */
    protected function generateJournalNumber()
    {
        $prefix = 'JE-' . date('Y') . '-';
        $lastJournal = Journal::where('company_id', user()->company_id)
            ->where('journal_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJournal) {
            $lastNumber = (int) str_replace($prefix, '', $lastJournal->journal_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Common accounting entries for different operations
     */

    // Payment Entry
    public function createPaymentEntry($amount, $paymentAccountId, $expenseAccountId, $description, $referenceType = null, $referenceId = null)
    {
        $entries = [
            [
                'account_id' => $expenseAccountId,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description
            ],
            [
                'account_id' => $paymentAccountId,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description
            ]
        ];

        return $this->createJournalEntry($entries, $description, $referenceType, $referenceId);
    }

    // Invoice Entry
    public function createInvoiceEntry($amount, $receivableAccountId, $revenueAccountId, $description, $referenceType = null, $referenceId = null)
    {
        $entries = [
            [
                'account_id' => $receivableAccountId,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description
            ],
            [
                'account_id' => $revenueAccountId,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description
            ]
        ];

        return $this->createJournalEntry($entries, $description, $referenceType, $referenceId);
    }

    // Payment Receipt Entry
    public function createPaymentReceiptEntry($amount, $cashAccountId, $receivableAccountId, $description, $referenceType = null, $referenceId = null)
    {
        $entries = [
            [
                'account_id' => $cashAccountId,
                'debit' => $amount,
                'credit' => 0,
                'description' => $description
            ],
            [
                'account_id' => $receivableAccountId,
                'debit' => 0,
                'credit' => $amount,
                'description' => $description
            ]
        ];

        return $this->createJournalEntry($entries, $description, $referenceType, $referenceId);
    }
}
