<?php

namespace Modules\Accounting\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\AccountCategory;
use Modules\Accounting\Entities\AccountType;
use Illuminate\Validation\Rule;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\DataTables\AccountsDataTable;

class AccountsController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.Accounts';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index(AccountsDataTable $dataTable)
    {
        $this->accounts = Account::get();
        return $dataTable->render('accounting::accounts.account.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function hierarchyData()
    // {

    //     $this->pageTitle = 'Accounts Hierarchy';

    //     $this->accountTypes = AccountType::with([
    //         'categories.accounts.childAccounts'
    //     ])->get();

    //     if (request()->ajax()) {
    //         return Reply::dataOnly(['status' => 'success', 'accountTypes' => $this->accountTypes]);
    //     }

    //     return view('accounting::accounts.account-hierarchy.index', $this->data);
    // }
    public function hierarchyData()
    {
        $this->pageTitle = 'Chart of Accounts';

        $this->accountTypes = AccountType::with([
            'categories.accounts.childAccounts'
        ])->get();
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'accountTypes' => $this->accountTypes
            ]);
        }

        return view('accounting::accounts.account-hierarchy.index', $this->data);
    }
    public function checkChildren($id)
    {
        try {
            // Find the account by ID
            $account = Account::findOrFail($id);

            // Get the child account IDs
            $childIds = $account->childAccounts->pluck('id')->toArray();

            // Check if there are any child accounts
            $hasChildren = !empty($childIds);

            return response()->json([
                'status' => 'success',
                'hasChildren' => $hasChildren,
                'childIds' => $childIds // Include the child account IDs in the response
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);
        } catch (\Exception $e) {
            // Log the actual error message
            Log::error('Error checking children for account ID ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking child accounts'
            ], 500);
        }
    }



    public function destroyMultiple(Request $request)
    {
        try {
            // Ensure the request contains account IDs
            $ids = $request->input('ids');

            // Check if the IDs array is not empty
            if (empty($ids)) {
                return response()->json(['status' => 'error', 'message' => 'No accounts selected for deletion.'], 400);
            }

            // Delete accounts and their associated child accounts
            Account::whereIn('id', $ids)->delete();

            return response()->json(['status' => 'success', 'message' => 'Accounts deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred while deleting the accounts.'], 500);
        }
    }


    public function create(Request $request)
    {
        // Get the id and type from the query parameters
        $this->id = $request->query('id');
        $this->type = $request->query('type');

        // Set default values for the page
        $this->pageTitle = __('app.addAccount');
        $this->company_id = user()->company_id;

        // Fetch account categories and parent accounts
        $this->accountCategories = AccountCategory::all();
        $this->parentAccounts = Account::all();


        // Define the view for AJAX request
        $this->view = 'accounting::accounts.account.ajax.create';

        // Check if it's an AJAX request
        if ($request->ajax()) {
            // Return the AJAX view
            return $this->returnAjax($this->view);
        }

        // Return the standard view for non-AJAX requests
        return view('accounting::accounts.account.create', $this->data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'account_category_id' => 'required|exists:account_categories,id',
            'account_parent_id' => 'nullable|exists:accounts,id',
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:accounts,code',
            'description' => 'nullable|string',
        ]);

        $account = new Account();
        $account->account_category_id = $request->account_category_id;
        $account->account_parent_id = $request->account_parent_id;
        $account-> company_id = $request->company_id;
        $account->name = $request->name;
        $account->code = $request->code;
        $account->description = $request->description;

        $account->save();
        return Reply::successWithData(__('messages.AccountAddedSuccessfully'), ['redirectUrl' => route('accounts.index')]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hotelmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     return view('hotelmanagement::edit');
    // }

    public function edit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.editAccount');

        $this->account = Account::findOrFail($id);
        // dd($this->account->all());
        $this->accountCategories = AccountCategory::all();
        $this->parentAccounts = Account::where('id', '!=', $id)->get();
        $this->view = 'accounting::accounts.account.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('accounting::accounts.account.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
        'account_category_id' => 'required|exists:account_categories,id',
        'account_parent_id' => 'nullable|exists:accounts,id',
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:accounts,code,' . $id,
        'description' => 'nullable|string',
        ]);

        $account = Account::findOrFail($id);
        $account->account_category_id = $request->account_category_id;
        $account->account_parent_id = $request->account_parent_id;
        $account->name = $request->name;
        $account->code = $request->code;
        $account->description = $request->description;
        $account->save();

        return Reply::successWithData(__('messages.AccountUpdatedSuccessfully'), ['redirectUrl' => route('accounts.index')]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $account = Account::findOrFail($id);
        $account->delete();

        return Reply::successWithData(__('messages.AccountDeletedSuccessfully'), ['redirectUrl' => route('accounts.index')]);
    }
}
