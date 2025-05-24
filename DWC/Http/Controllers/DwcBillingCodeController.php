<?php

namespace Modules\DWC\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DWC\DataTables\DwcBillingCodeDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\Salutation;
use Modules\DWC\Entities\DwcHotel;
use App\Http\Controllers\AccountBaseController;
use Modules\DWC\Entities\DwcHorse;
use Modules\DWC\Entities\DwcHotelRoomType;
use App\Helper\Reply;
use DB;
use Modules\DWC\Entities\DwcFlightTicket;
use Modules\DWC\Entities\DwcBillingCode;
use Modules\DWC\Entities\DWCHorse as EntitiesDWCHorse;
use Modules\DWC\Entities\DwcHotelReservation;

class DwcBillingCodeController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.BillingCodes';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */

    public function index(DwcBillingCodeDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->DwcBillingCode = DwcBillingCode::all();
            // $this->skills = Skill::all();
        }

        // dd($this->data);

        return $dataTable->render('dwc::billing_code.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.addBillingCode');
        $this->company_id = user()->company_id;
        $this->view = 'dwc::billing_code.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('dwc::billing_code.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dwc_billing_codes,name',
            'position' => 'nullable|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $BillingCode = new DwcBillingCode();
        $BillingCode->name = $request->name;
        $BillingCode->position = $request->position;
        $BillingCode->company_id = $request->company_id;
        $BillingCode->save();

        return Reply::successWithData(__('messages.BillingCodeAddedSuccessfully'), [
            'redirectUrl' => route('billing-code.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.EditBillingCode');

        $this->BillingCodes = DwcBillingCode::findOrFail($id);

        $this->view = 'dwc::billing_code.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        // return view('dwc::edit');
        return view('dwc::billing_code.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dwc_billing_codes,name,' . $id,
            'position' => 'nullable|string|max:255',
        ]);

        $BillingCode = DwcBillingCode::findOrFail($id);
        $BillingCode->name = $request->name;
        $BillingCode->position = $request->position;
        $BillingCode->save();

        return Reply::successWithData(__('messages.BillingCodeUpdatedSuccessfully'), [
            'redirectUrl' => route('billing-code.index')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $DwcBillingCode = DwcBillingCode::findOrFail($id);
        $DwcBillingCode->delete();

        return Reply::successWithData(__('messages.DwcBillingCodeDeletedSuccessfully'), ['redirectUrl' => route('billing-code.index')]);
    }
}
