<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\Http\Controllers\AccountingSettingController;
use Modules\Accounting\Http\Controllers\BudgetController;
use Modules\Accounting\Http\Controllers\ChartOfAccountController;
use Modules\Accounting\Http\Controllers\ClosingEntryController;
use Modules\Accounting\Http\Controllers\FiscalYearController;
use Modules\Accounting\Http\Controllers\ImportExportController;
use Modules\Accounting\Http\Controllers\JournalController;
use Modules\Accounting\Http\Controllers\ReconciliationController;
use Modules\Accounting\Http\Controllers\ReportController;
use Modules\Accounting\Http\Controllers\TaxCodeController;

Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    // Main accounting routes
    Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('accounting/journal-entry', [AccountingController::class, 'createJournalEntry'])->name('accounting.journal-entry');

    // Chart of Accounts
    Route::resource('chart-of-accounts', ChartOfAccountController::class)->names('accounting.chart-of-accounts');

    // Journals
    Route::resource('journals', JournalController::class)->names('accounting.journals');
    Route::post('journals/{journal}/post', [JournalController::class, 'post'])->name('accounting.journals.post');
    Route::post('journals/{journal}/reverse', [JournalController::class, 'reverse'])->name('accounting.journals.reverse');


    // Budgets
    Route::resource('budgets', BudgetController::class)->names('accounting.budgets');

    // Fiscal Years
    Route::resource('fiscal-years', FiscalYearController::class)->names('accounting.fiscal-years');

    // Reconciliations
    Route::resource('reconciliations', ReconciliationController::class)->names('accounting.reconciliations');

    // Tax Codes
    Route::resource('tax-codes', TaxCodeController::class)->names('accounting.tax-codes');

    // Closing Entries
    Route::get('closing-entries', [ClosingEntryController::class, 'index'])->name('accounting.closing-entries.index');
    Route::post('closing-entries/close', [ClosingEntryController::class, 'close'])->name('accounting.closing-entries.close');

    // Settings
    Route::get('settings', [AccountingSettingController::class, 'index'])->name('accounting.settings.index');
    Route::put('settings', [AccountingSettingController::class, 'update'])->name('accounting.settings.update');

    // Import/Export
    Route::get('import-export', [ImportExportController::class, 'index'])->name('accounting.import-export.index');
    Route::post('import/chart-of-accounts', [ImportExportController::class, 'importChartOfAccounts'])->name('accounting.import.chart-of-accounts');
    Route::get('export/chart-of-accounts', [ImportExportController::class, 'exportChartOfAccounts'])->name('accounting.export.chart-of-accounts');


    // Reports
    Route::get('reports/trial-balance', [ReportController::class, 'trialBalance'])->name('accounting.reports.trial-balance');
    Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('accounting.reports.balance-sheet');
    Route::get('reports/income-statement', [ReportController::class, 'incomeStatement'])->name('accounting.reports.income-statement');
    Route::get('reports/general-ledger', [ReportController::class, 'generalLedger'])->name('accounting.reports.general-ledger');
});

// API Routes for integration
Route::group(['middleware' => 'auth', 'prefix' => 'api/accounting'], function () {
    Route::post('create-entry', [AccountingController::class, 'createJournalEntry'])->name('api.accounting.create-entry');
    Route::get('accounts', [ChartOfAccountController::class, 'getAccounts'])->name('api.accounting.accounts');
});
