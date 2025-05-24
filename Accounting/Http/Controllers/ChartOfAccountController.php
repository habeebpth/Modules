<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\DataTables\ChartOfAccountDataTable;

class ChartOfAccountController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Chart of Accounts';
        
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index(ChartOfAccountDataTable $dataTable)
    {
        return $dataTable->render('accounting::chart-of-accounts.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Add Account';
        $this->parentAccounts = ChartOfAccount::where('company_id', user()->company_id)
            ->whereNull('parent_id')
            ->get();
        $this->accountTypes = ChartOfAccount::ACCOUNT_TYPES;
        $this->accountSubTypes = ChartOfAccount::ACCOUNT_SUB_TYPES;
        
        $this->view = 'accounting::chart-of-accounts.ajax.create';
        
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        
        return view('accounting::chart-of-accounts.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', array_keys(ChartOfAccount::ACCOUNT_TYPES)),
            'account_sub_type' => 'required|string',
            'opening_balance' => 'nullable|numeric',
        ]);

        $account = ChartOfAccount::create([
            'company_id' => user()->company_id,
            'parent_id' => $request->parent_id,
            'account_code' => $request->account_code,
            'account_name' => $request->account_name,
            'account_type' => $request->account_type,
            'account_sub_type' => $request->account_sub_type,
            'description' => $request->description,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'is_active' => true,
        ]);

        return Reply::successWithData('Account created successfully', [
            'redirectUrl' => route('accounting.chart-of-accounts.index')
        ]);
    }

    public function edit($id)
    {
        $this->account = ChartOfAccount::findOrFail($id);
        $this->pageTitle = 'Edit Account';
        $this->parentAccounts = ChartOfAccount::where('company_id', user()->company_id)
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();
        $this->accountTypes = ChartOfAccount::ACCOUNT_TYPES;
        $this->accountSubTypes = ChartOfAccount::ACCOUNT_SUB_TYPES;
        
        $this->view = 'accounting::chart-of-accounts.ajax.edit';
        
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        
        return view('accounting::chart-of-accounts.edit', $this->data);
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts,account_code,' . $id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', array_keys(ChartOfAccount::ACCOUNT_TYPES)),
            'account_sub_type' => 'required|string',
        ]);

        $account->update([
            'parent_id' => $request->parent_id,
            'account_code' => $request->account_code,
            'account_name' => $request->account_name,
            'account_type' => $request->account_type,
            'account_sub_type' => $request->account_sub_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return Reply::successWithData('Account updated successfully', [
            'redirectUrl' => route('accounting.chart-of-accounts.index')
        ]);
    }

    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        // Check if account has journal entries
        if ($account->journalEntries()->exists()) {
            return Reply::error('Cannot delete account with existing journal entries');
        }
        
        // Check if account has children
        if ($account->children()->exists()) {
            return Reply::error('Cannot delete account with sub-accounts');
        }
        
        $account->delete();
        
        return Reply::success('Account deleted successfully');
    }

    // API method for getting accounts
    public function getAccounts(Request $request)
    {
        $accounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('is_active', true);
            
        if ($request->type) {
            $accounts->where('account_type', $request->type);
        }
        
        if ($request->search) {
            $accounts->where(function($q) use ($request) {
                $q->where('account_name', 'like', '%' . $request->search . '%')
                  ->orWhere('account_code', 'like', '%' . $request->search . '%');
            });
        }
        
        return $accounts->get(['id', 'account_code', 'account_name', 'account_type', 'current_balance']);
    }
}