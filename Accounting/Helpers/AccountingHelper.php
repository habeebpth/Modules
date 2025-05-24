<?php

if (!function_exists('createAccountingEntry')) {
    /**
     * Global helper to create accounting entries from anywhere
     */
    function createAccountingEntry($entries, $description, $referenceType = null, $referenceId = null, $date = null)
    {
        if (!in_array('accounting', user()->modules ?? [])) {
            return false;
        }

        try {
            $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
            return $accountingService->createJournalEntry($entries, $description, $referenceType, $referenceId, $date);
        } catch (\Exception $e) {
            \Log::error('Accounting Entry Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('createPaymentAccountingEntry')) {
    /**
     * Helper for payment accounting entries
     */
    function createPaymentAccountingEntry($amount, $paymentAccountId, $expenseAccountId, $description, $referenceType = null, $referenceId = null)
    {
        if (!in_array('accounting', user()->modules ?? [])) {
            return false;
        }

        try {
            $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
            return $accountingService->createPaymentEntry($amount, $paymentAccountId, $expenseAccountId, $description, $referenceType, $referenceId);
        } catch (\Exception $e) {
            \Log::error('Payment Accounting Entry Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('createInvoiceAccountingEntry')) {
    /**
     * Helper for invoice accounting entries
     */
    function createInvoiceAccountingEntry($amount, $receivableAccountId, $revenueAccountId, $description, $referenceType = null, $referenceId = null)
    {
        if (!in_array('accounting', user()->modules ?? [])) {
            return false;
        }

        try {
            $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
            return $accountingService->createInvoiceEntry($amount, $receivableAccountId, $revenueAccountId, $description, $referenceType, $referenceId);
        } catch (\Exception $e) {
            \Log::error('Invoice Accounting Entry Error: ' . $e->getMessage());
            return false;
        }
    }

    if (!function_exists('createHotelAccountingEntry')) {
        function createHotelAccountingEntry($amount, $description, $referenceId = null)
        {
            if (!in_array('accounting', user()->modules ?? [])) {
                return false;
            }
    
            try {
                $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
                return $accountingService->createHotelRevenueEntry($amount, $description, $referenceId);
            } catch (\Exception $e) {
                \Log::error('Hotel Accounting Entry Error: ' . $e->getMessage());
                return false;
            }
        }
    }
    
    if (!function_exists('getAccountingAccount')) {
        function getAccountingAccount($accountCode)
        {
            if (!in_array('accounting', user()->modules ?? [])) {
                return null;
            }
    
            return \Modules\Accounting\Entities\ChartOfAccount::where('company_id', user()->company_id)
                ->where('account_code', $accountCode)
                ->where('is_active', true)
                ->first();
        }
    }
    
    if (!function_exists('getAccountBalance')) {
        function getAccountBalance($accountId, $asOfDate = null)
        {
            if (!in_array('accounting', user()->modules ?? [])) {
                return 0;
            }
    
            $account = \Modules\Accounting\Entities\ChartOfAccount::find($accountId);
            if (!$account) return 0;
    
            $query = $account->journalEntries();
            
            if ($asOfDate) {
                $query->whereHas('journal', function($q) use ($asOfDate) {
                    $q->where('date', '<=', $asOfDate)
                      ->where('status', 'posted');
                });
            }
    
            $debitTotal = $query->sum('debit');
            $creditTotal = $query->sum('credit');
    
            // Return balance based on account type
            if (in_array($account->account_type, ['asset', 'expense'])) {
                return $debitTotal - $creditTotal; // Normal debit balance
            } else {
                return $creditTotal - $debitTotal; // Normal credit balance
            }
        }
    }
}