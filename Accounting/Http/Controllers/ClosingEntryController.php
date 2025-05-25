<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\ClosingEntry;
use Modules\Accounting\Entities\FiscalYear;
use Modules\Accounting\Services\ClosingService;

class ClosingEntryController extends AccountBaseController
{
    protected $closingService;

    public function __construct(ClosingService $closingService)
    {
        parent::__construct();
        $this->closingService = $closingService;
        $this->pageTitle = 'Year End Closing';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->fiscalYears = FiscalYear::where('company_id', user()->company_id)->get();
        $this->closingEntries = ClosingEntry::where('company_id', user()->company_id)
            ->with(['fiscalYear', 'journal'])
            ->get();

        return view('accounting::closing-entries.index', $this->data);
    }

    public function close(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
        ]);

        try {
            $this->closingService->performYearEndClose($request->fiscal_year_id);
            return Reply::success('Year end closing completed successfully');
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }
}
