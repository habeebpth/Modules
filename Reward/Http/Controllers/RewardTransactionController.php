<?php

namespace Modules\Reward\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use App\Helper\Files;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Reward\Entities\RewardTransaction;
use Modules\Reward\Entities\RewardCustomer;
use Modules\Reward\DataTables\RewardTransactionDataTable;

class RewardTransactionController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.RewardTransactions';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));
            // abort_403(!in_array('hotelmanagement', $this->user->modules));
            return $next($request);
        });
    }
    public function index(RewardTransactionDataTable $dataTable)
    {
        $this->RewardTransaction = RewardTransaction::get();
        return $dataTable->render('reward::reward-transaction.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.Reward');
        $this->company_id = user()->company_id;
        $this->customers = User::allClients();
        $this->view = 'reward::reward-transaction.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('reward::reward-transaction.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */

  public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'customer_id' => 'required|exists:users,id',
        'transaction_type' => 'required|in:Earn,Redeem,Adjust',
        'points' => 'required|integer|min:0',
        'reference_type' => 'nullable|string|max:50',
        'reference_id' => 'nullable|integer',
        'transaction_date' => 'required|date',
        'status' => 'nullable|string|max:255',
        'earned_from' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
        'company_id' => 'required|exists:companies,id',
    ]);

    // Create and save new RewardTransaction
    $transaction = new RewardTransaction();
    $transaction->customer_id = $request->customer_id;
    $transaction->transaction_type = $request->transaction_type;
    $transaction->points = $request->points;
    $transaction->reference_type = $request->reference_type;
    $transaction->reference_id = $request->reference_id;
    $transaction->transaction_date = companyToYmd($request->transaction_date);
    $transaction->status = $request->status;
    $transaction->earned_from = $request->earned_from;
    $transaction->remarks = $request->remarks;
    $transaction->company_id = $request->company_id;
    $transaction->save();

    // Update RewardCustomer points if record exists
    $rewardCustomer = RewardCustomer::where('customer_id', $request->customer_id)->first();

    if ($rewardCustomer) {
        if ($request->transaction_type === 'Earn') {
            $rewardCustomer->total_points_earned += $request->points;
        } elseif ($request->transaction_type === 'Redeem') {
            $rewardCustomer->total_points_redeemed += $request->points;
        }
        $rewardCustomer->save();
    }

    return Reply::successWithData(__('messages.recordSaved'), [
        'redirectUrl' => route('reward-transactions.index'),
    ]);
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
        $this->pageTitle = __('app.Reward');

        $this->transaction = RewardTransaction::findOrFail($id);
             $this->customers = User::allClients();

        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'reward::reward-transaction.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('reward::reward-transaction.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    // Validate the request
    $request->validate([
        'customer_id' => 'required|exists:users,id',
        'transaction_type' => 'required|in:Earn,Redeem,Adjust',
        'points' => 'required|integer|min:0',
        'reference_type' => 'nullable|string|max:50',
        'reference_id' => 'nullable|integer',
        'transaction_date' => 'required|date',
        'status' => 'nullable|string|max:255',
        'earned_from' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
    ]);

    $transaction = RewardTransaction::findOrFail($id);

    // Calculate point difference if type is Earn or Redeem
    $originalPoints = $transaction->points;
    $originalType = $transaction->transaction_type;
    $customerId = $transaction->customer_id;

    // Update transaction
    $transaction->customer_id = $request->customer_id;
    $transaction->transaction_type = $request->transaction_type;
    $transaction->points = $request->points;
    $transaction->reference_type = $request->reference_type;
    $transaction->reference_id = $request->reference_id;
    $transaction->transaction_date = companyToYmd($request->transaction_date);
    $transaction->status = $request->status;
    $transaction->earned_from = $request->earned_from;
    $transaction->remarks = $request->remarks;
    $transaction->save();

    // Update RewardCustomer points if applicable
    $rewardCustomer = RewardCustomer::where('customer_id', $customerId)->first();

    if ($rewardCustomer) {
        // Revert old transaction
        if ($originalType === 'Earn') {
            $rewardCustomer->total_points_earned -= $originalPoints;
        } elseif ($originalType === 'Redeem') {
            $rewardCustomer->total_points_redeemed -= $originalPoints;
        }

        // Apply new transaction
        if ($request->transaction_type === 'Earn') {
            $rewardCustomer->total_points_earned += $request->points;
        } elseif ($request->transaction_type === 'Redeem') {
            $rewardCustomer->total_points_redeemed += $request->points;
        }

        $rewardCustomer->save();
    }

    return Reply::successWithData(__('messages.updateSuccess'), [
        'redirectUrl' => route('reward-transactions.index'),
    ]);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $RewardTransaction = RewardTransaction::findOrFail($id);
        $RewardTransaction->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('reward-transactions.index')]);
    }
}
