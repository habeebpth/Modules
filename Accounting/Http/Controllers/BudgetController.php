<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Budget;
use Modules\Accounting\Entities\FiscalYear;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\DataTables\BudgetDataTable;

class BudgetController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Budgets';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index(BudgetDataTable $dataTable)
    {
        $this->fiscalYears = FiscalYear::where('company_id', user()->company_id)->get();
        return $dataTable->render('accounting::budgets.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Create Budget';
        $this->fiscalYears = FiscalYear::where('company_id', user()->company_id)->active()->get();
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)->active()->get();

        return view('accounting::budgets.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'period_type' => 'required|in:monthly,quarterly,yearly',
            'period_number' => 'required|integer|min:1',
            'budgeted_amount' => 'required|numeric|min:0',
        ]);

        Budget::create($request->all() + ['company_id' => user()->company_id]);

        return Reply::successWithData('Budget created successfully', [
            'redirectUrl' => route('accounting.budgets.index')
        ]);
    }
}
