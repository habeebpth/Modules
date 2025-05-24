<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Services\ReportService;
use Carbon\Carbon;

class ReportController extends AccountBaseController
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
        
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function trialBalance(Request $request)
    {
        $this->pageTitle = 'Trial Balance';
        
        $asOf = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();
        $this->asOfDate = $asOf->format('Y-m-d');
        
        $this->trialBalance = $this->reportService->getTrialBalance($asOf);
        
        return view('accounting::reports.trial-balance', $this->data);
    }

    public function balanceSheet(Request $request)
    {
        $this->pageTitle = 'Balance Sheet';
        
        $asOf = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();
        $this->asOfDate = $asOf->format('Y-m-d');
        
        $this->balanceSheet = $this->reportService->getBalanceSheet($asOf);
        
        return view('accounting::reports.balance-sheet', $this->data);
    }

    public function incomeStatement(Request $request)
    {
        $this->pageTitle = 'Income Statement';
        
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : now();
        
        $this->fromDate = $fromDate->format('Y-m-d');
        $this->toDate = $toDate->format('Y-m-d');
        
        $this->incomeStatement = $this->reportService->getIncomeStatement($fromDate, $toDate);
        
        return view('accounting::reports.income-statement', $this->data);
    }

    public function generalLedger(Request $request)
    {
        $this->pageTitle = 'General Ledger';
        
        $accountId = $request->account_id;
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : now();
        
        $this->fromDate = $fromDate->format('Y-m-d');
        $this->toDate = $toDate->format('Y-m-d');
        
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();
            
        if ($accountId) {
            $this->selectedAccount = ChartOfAccount::findOrFail($accountId);
            $this->ledgerEntries = $this->reportService->getGeneralLedger($accountId, $fromDate, $toDate);
        }
        
        return view('accounting::reports.general-ledger', $this->data);
    }
}