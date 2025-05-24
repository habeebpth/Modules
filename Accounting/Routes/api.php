<?php
use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\Http\Controllers\ChartOfAccountController;

Route::group(['middleware' => 'auth', 'prefix' => 'accounting'], function () {
    // Create journal entry from other modules
    Route::post('journal-entry', [AccountingController::class, 'createJournalEntry'])
         ->name('api.accounting.journal-entry');
    
    // Get accounts for dropdowns
    Route::get('accounts', [ChartOfAccountController::class, 'getAccounts'])
         ->name('api.accounting.accounts');
    
    // Hotel specific entries
    Route::post('hotel-revenue', function(Request $request) {
        $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
        return $accountingService->createHotelRevenueEntry(
            $request->amount,
            $request->description,
            $request->reference_id
        );
    })->name('api.accounting.hotel-revenue');
    
    // Purchase specific entries
    Route::post('purchase-entry', function(Request $request) {
        $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
        return $accountingService->createPurchaseEntry(
            $request->amount,
            $request->description,
            $request->reference_id
        );
    })->name('api.accounting.purchase-entry');
    
    // Payroll specific entries
    Route::post('payroll-entry', function(Request $request) {
        $accountingService = app(\Modules\Accounting\Services\AccountingService::class);
        return $accountingService->createPayrollEntry(
            $request->gross_pay,
            $request->taxes,
            $request->net_pay,
            $request->description,
            $request->reference_id
        );
    })->name('api.accounting.payroll-entry');
});