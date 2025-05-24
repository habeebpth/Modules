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
use Modules\Reward\Entities\RewardCustomer;
use Modules\Reward\DataTables\RewardCustomerDataTable;

class RewardCustomerController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.RewardCustomers';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));
            // abort_403(!in_array('hotelmanagement', $this->user->modules));
            return $next($request);
        });
    }
    public function index(RewardCustomerDataTable $dataTable)
    {
        $this->RewardCustomer = RewardCustomer::get();
        return $dataTable->render('reward::reward-customer.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.Reward');
        $this->company_id = user()->company_id;
        $this->customers = User::allClients();
        $this->view = 'reward::reward-customer.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('reward::reward-customer.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'customer_id' => 'required|integer|unique:reward_customers,customer_id',
        'total_points_earned' => 'required|integer|min:0',
        'total_points_redeemed' => 'required|integer|min:0',
        'onhold_balance' => 'required|integer|min:0',
        'company_id' => 'required|integer|exists:companies,id',
    ]);

    // Create and save new RewardCustomer
    $rewardCustomer = new RewardCustomer();
    $rewardCustomer->customer_id = $request->customer_id;
    $rewardCustomer->total_points_earned = $request->total_points_earned;
    $rewardCustomer->total_points_redeemed = $request->total_points_redeemed;
    $rewardCustomer->onhold_balance = $request->onhold_balance;
    $rewardCustomer->company_id = $request->company_id;

    $rewardCustomer->save();

    return Reply::successWithData(__('messages.recordSaved'), [
        'redirectUrl' => route('reward-customers.index')
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

        $this->rewardCustomer = RewardCustomer::findOrFail($id);
             $this->customers = User::allClients();

        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'reward::reward-customer.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('reward::reward-customer.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    // Validate the request
    $request->validate([
        'customer_id' => 'required|integer|exists:users,id|unique:reward_customers,customer_id,' . $id,
        'total_points_earned' => 'required|integer|min:0',
        'total_points_redeemed' => 'required|integer|min:0',
        'onhold_balance' => 'required|integer|min:0',
    ]);

    // Find the existing reward customer record
    $rewardCustomer = RewardCustomer::findOrFail($id);

    // Update fields
    $rewardCustomer->customer_id = $request->customer_id;
    $rewardCustomer->total_points_earned = $request->total_points_earned;
    $rewardCustomer->total_points_redeemed = $request->total_points_redeemed;
    $rewardCustomer->onhold_balance = $request->onhold_balance;

    $rewardCustomer->save();

    return Reply::successWithData(__('messages.updateSuccess'), [
        'redirectUrl' => route('reward-customers.index')
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $RewardCustomer = RewardCustomer::findOrFail($id);
        $RewardCustomer->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('reward-customers.index')]);
    }
}
