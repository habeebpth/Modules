<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Services\AccountingService;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Entities\Journal;

class AccountingController extends AccountBaseController
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        parent::__construct();
        $this->accountingService = $accountingService;
        $this->pageTitle = 'Accounting Dashboard';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->totalAccounts = ChartOfAccount::where('company_id', user()->company_id)->count();
        $this->totalJournals = Journal::where('company_id', user()->company_id)
            ->where('status', Journal::STATUS_POSTED)->count();

        // Financial summary
        $this->assets = $this->accountingService->getAccountTypeBalance('asset');
        $this->liabilities = $this->accountingService->getAccountTypeBalance('liability');
        $this->equity = $this->accountingService->getAccountTypeBalance('equity');
        $this->revenue = $this->accountingService->getAccountTypeBalance('revenue');
        $this->expenses = $this->accountingService->getAccountTypeBalance('expense');

        return view('accounting::dashboard', $this->data);
    }

    // Create journal entry from other modules
    public function createJournalEntry(Request $request)
    {
        try {
            $journal = $this->accountingService->createJournalEntry(
                $request->entries,
                $request->description,
                $request->reference_type,
                $request->reference_id,
                $request->date ?? now()
            );

            return Reply::successWithData('Journal entry created successfully', $journal);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }
}
