<?php
return [
    'accounting' => 'Accounting',
    'dashboard' => 'Dashboard',
    'chartOfAccounts' => 'Chart of Accounts',
    'journalEntries' => 'Journal Entries',
    'reports' => 'Reports',

    // Account related
    'account' => 'Account',
    'accountCode' => 'Account Code',
    'accountName' => 'Account Name',
    'accountType' => 'Account Type',
    'accountSubType' => 'Account Sub Type',
    'parentAccount' => 'Parent Account',
    'openingBalance' => 'Opening Balance',
    'currentBalance' => 'Current Balance',
    'addAccount' => 'Add Account',
    'editAccount' => 'Edit Account',
    'deleteAccount' => 'Delete Account',

    // Journal related
    'journal' => 'Journal',
    'journalEntry' => 'Journal Entry',
    'journalNumber' => 'Journal Number',
    'createJournalEntry' => 'Create Journal Entry',
    'editJournalEntry' => 'Edit Journal Entry',
    'postJournalEntry' => 'Post Journal Entry',
    'reverseJournalEntry' => 'Reverse Journal Entry',
    'debit' => 'Debit',
    'credit' => 'Credit',
    'description' => 'Description',
    'reference' => 'Reference',
    'date' => 'Date',
    'status' => 'Status',
    'draft' => 'Draft',
    'posted' => 'Posted',
    'reversed' => 'Reversed',

    // Report related
    'trialBalance' => 'Trial Balance',
    'balanceSheet' => 'Balance Sheet',
    'incomeStatement' => 'Income Statement',
    'generalLedger' => 'General Ledger',
    'asOfDate' => 'As of Date',
    'fromDate' => 'From Date',
    'toDate' => 'To Date',
    'dateRange' => 'Date Range',

    // Financial terms
    'assets' => 'Assets',
    'liabilities' => 'Liabilities',
    'equity' => 'Equity',
    'revenue' => 'Revenue',
    'expenses' => 'Expenses',
    'netIncome' => 'Net Income',
    'grossProfit' => 'Gross Profit',
    'operatingIncome' => 'Operating Income',
    'totalAssets' => 'Total Assets',
    'totalLiabilities' => 'Total Liabilities',
    'totalEquity' => 'Total Equity',
    'currentAssets' => 'Current Assets',
    'fixedAssets' => 'Fixed Assets',
    'currentLiabilities' => 'Current Liabilities',
    'longTermLiabilities' => 'Long Term Liabilities',

    // Account types
    'asset' => 'Asset',
    'liability' => 'Liability',
    'equity' => 'Equity',
    'revenue' => 'Revenue',
    'expense' => 'Expense',

    // Messages
    'accountCreated' => 'Account created successfully',
    'accountUpdated' => 'Account updated successfully',
    'accountDeleted' => 'Account deleted successfully',
    'journalCreated' => 'Journal entry created successfully',
    'journalUpdated' => 'Journal entry updated successfully',
    'journalPosted' => 'Journal entry posted successfully',
    'journalReversed' => 'Journal entry reversed successfully',
    'journalNotBalanced' => 'Journal entry is not balanced. Total debits must equal total credits.',
    'cannotDeleteAccountWithEntries' => 'Cannot delete account with existing journal entries',
    'cannotDeleteAccountWithChildren' => 'Cannot delete account with sub-accounts',
    'onlyDraftCanBeEdited' => 'Only draft journal entries can be edited',
    'onlyPostedCanBeReversed' => 'Only posted journal entries can be reversed',


    // Budget related
    'budget' => 'Budget',
    'budgets' => 'Budgets',
    'budgetedAmount' => 'Budgeted Amount',
    'actualAmount' => 'Actual Amount',
    'variance' => 'Variance',
    'periodType' => 'Period Type',
    'periodNumber' => 'Period Number',
    'monthly' => 'Monthly',
    'quarterly' => 'Quarterly',
    'yearly' => 'Yearly',

    // Fiscal Year related
    'fiscalYear' => 'Fiscal Year',
    'fiscalYears' => 'Fiscal Years',
    'startDate' => 'Start Date',
    'endDate' => 'End Date',
    'isClosed' => 'Is Closed',

    // Reconciliation related
    'reconciliation' => 'Reconciliation',
    'reconciliations' => 'Reconciliations',
    'statementBalance' => 'Statement Balance',
    'bookBalance' => 'Book Balance',
    'difference' => 'Difference',
    'reconciliationDate' => 'Reconciliation Date',

    // Tax related
    'taxCode' => 'Tax Code',
    'taxCodes' => 'Tax Codes',
    'taxRate' => 'Tax Rate',
    'taxAccount' => 'Tax Account',
    'salesTax' => 'Sales Tax',
    'purchaseTax' => 'Purchase Tax',

    // Closing entries
    'closingEntry' => 'Closing Entry',
    'closingEntries' => 'Closing Entries',
    'yearEndClosing' => 'Year End Closing',
    'closingDate' => 'Closing Date',

    // Settings
    'accountingSettings' => 'Accounting Settings',
    'autoPostJournals' => 'Auto Post Journals',
    'requireReference' => 'Require Reference',
    'allowFutureDates' => 'Allow Future Dates',
    'defaultAccounts' => 'Default Accounts',

    // Import/Export
    'importExport' => 'Import/Export',
    'importChartOfAccounts' => 'Import Chart of Accounts',
    'exportChartOfAccounts' => 'Export Chart of Accounts',
    'importFile' => 'Import File',
    'downloadTemplate' => 'Download Template',

    'setup' => 'Setup',
    'transactions' => 'Transactions',
    'budgeting' => 'Budgeting',
    'yearEnd' => 'Year-End',
    'tools' => 'Tools',
    'newJournalEntry' => 'New Journal Entry',
    'createBudget' => 'Create Budget',
    'bankReconciliation' => 'Bank Reconciliation',
    'budgetVsActual' => 'Budget vs Actual',
    'cashFlow' => 'Cash Flow',
    'closingEntries' => 'Closing Entries',
    'auditTrail' => 'Audit Trail',
];
