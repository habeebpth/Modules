<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Reconciliation;
use Modules\Accounting\Entities\ChartOfAccount;

class ReconciliationController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Bank Reconciliation';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->reconciliations = Reconciliation::where('company_id', user()->company_id)
            ->with('account')
            ->orderBy('reconciliation_date', 'desc')
            ->get();

        return view('accounting::reconciliations.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Create Reconciliation';
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', 'asset')
            ->where('account_sub_type', 'current_asset')
            ->active()
            ->get();

        return view('accounting::reconciliations.create', $this->data);
    }
}
