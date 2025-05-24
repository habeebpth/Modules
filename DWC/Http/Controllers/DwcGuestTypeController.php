<?php

namespace Modules\DWC\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DWC\DataTables\DwcGuestTypeDataTable;
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
use Modules\DWC\Entities\DwcGuestType;
use Modules\DWC\Entities\DWCHorse as EntitiesDWCHorse;
use Modules\DWC\Entities\DwcHotelReservation;

class DwcGuestTypeController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.Guesttypes';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */

    public function index(DwcGuestTypeDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->DwcGuestType = DwcGuestType::all();
            // $this->skills = Skill::all();
        }

        // dd($this->data);

        return $dataTable->render('dwc::guest-type.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.addGuesttype');
        $this->company_id = user()->company_id;
        $this->view = 'dwc::guest-type.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('dwc::guest-type.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dwc_guest_types,name',
            'position' => 'nullable|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $guestType = new DwcGuestType();
        $guestType->name = $request->name;
        $guestType->position = $request->position;
        $guestType->company_id = $request->company_id;
        $guestType->save();

        return Reply::successWithData(__('messages.guestTypeAddedSuccessfully'), [
            'redirectUrl' => route('guest-type.index')
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->hotels = DwcHotel::findOrFail($id);

        $tab = request('tab');

        switch ($tab) {

            case 'roomtype':
                $this->view = 'dwc::hotel.ajax.roomtype';
                break;
            default:
                $this->view = 'dwc::hotel.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('dwc::hotel.show', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.EditGuesttype');

        $this->guesttypes = DwcGuestType::findOrFail($id);

        $this->view = 'dwc::guest-type.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        // return view('dwc::edit');
        return view('dwc::guest-type.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:dwc_guest_types,name,' . $id,
            'position' => 'nullable|string|max:255',
        ]);

        $guestType = DwcGuestType::findOrFail($id);
        $guestType->name = $request->name;
        $guestType->position = $request->position;
        $guestType->save();

        return Reply::successWithData(__('messages.guestTypeUpdatedSuccessfully'), [
            'redirectUrl' => route('guest-type.index')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $DwcGuestType = DwcGuestType::findOrFail($id);
        $DwcGuestType->delete();

        return Reply::successWithData(__('messages.DwcGuestTypeDeletedSuccessfully'), ['redirectUrl' => route('guest-type.index')]);
    }
}
