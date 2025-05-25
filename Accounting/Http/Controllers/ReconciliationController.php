<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Reconciliation;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\ReconciliationService;

class ReconciliationController extends AccountBaseController
{
    protected $reconciliationService;

    public function __construct(ReconciliationService $reconciliationService)
    {
        parent::__construct();
        $this->reconciliationService = $reconciliationService;
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
            ->where('is_active', true)
            ->get();

        if (request()->ajax()) {
            $this->view = 'accounting::reconciliations.ajax.create';
            return $this->returnAjax($this->view);
        }

        return view('accounting::reconciliations.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'reconciliation_date' => 'required|date',
            'statement_balance' => 'required|numeric',
        ]);

        try {
            $reconciliation = $this->reconciliationService->createReconciliation(
                $request->account_id,
                $request->reconciliation_date,
                $request->statement_balance
            );

            $reconciliation->update([
                'notes' => $request->notes,
                'created_by' => user()->id
            ]);

            return Reply::successWithData('Reconciliation created successfully', [
                'redirectUrl' => route('accounting.reconciliations.index')
            ]);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function show($id)
    {
        $this->reconciliation = Reconciliation::with('account')->findOrFail($id);
        $this->pageTitle = 'Reconciliation Details';

        return view('accounting::reconciliations.show', $this->data);
    }

    public function edit($id)
    {
        $this->reconciliation = Reconciliation::findOrFail($id);

        if ($this->reconciliation->status !== 'draft') {
            return Reply::error('Only draft reconciliations can be edited');
        }

        $this->pageTitle = 'Edit Reconciliation';
        $this->accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', 'asset')
            ->where('account_sub_type', 'current_asset')
            ->where('is_active', true)
            ->get();

        if (request()->ajax()) {
            $this->view = 'accounting::reconciliations.ajax.edit';
            return $this->returnAjax($this->view);
        }

        return view('accounting::reconciliations.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $reconciliation = Reconciliation::findOrFail($id);

        if ($reconciliation->status !== 'draft') {
            return Reply::error('Only draft reconciliations can be updated');
        }

        $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'reconciliation_date' => 'required|date',
            'statement_balance' => 'required|numeric',
        ]);

        try {
            // Recalculate book balance
            $bookBalance = $this->reconciliationService->calculateBookBalance(
                $request->account_id,
                $request->reconciliation_date
            );

            $reconciliation->update([
                'account_id' => $request->account_id,
                'reconciliation_date' => $request->reconciliation_date,
                'statement_balance' => $request->statement_balance,
                'book_balance' => $bookBalance,
                'difference' => $request->statement_balance - $bookBalance,
                'notes' => $request->notes,
            ]);

            return Reply::successWithData('Reconciliation updated successfully', [
                'redirectUrl' => route('accounting.reconciliations.index')
            ]);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    // API method to get book balance for reconciliation
    public function getBookBalance(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:chart_of_accounts,id',
            'date' => 'required|date',
        ]);

        try {
            $balance = $this->reconciliationService->calculateBookBalance(
                $request->account_id,
                $request->date
            );

            return response()->json([
                'status' => 'success',
                'balance' => number_format($balance, 2, '.', ''),
                'formatted_balance' => currency_format($balance)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
