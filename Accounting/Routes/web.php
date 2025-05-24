<?php
use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\Http\Controllers\ChartOfAccountController;
use Modules\Accounting\Http\Controllers\JournalController;
use Modules\Accounting\Http\Controllers\ReportController;

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
