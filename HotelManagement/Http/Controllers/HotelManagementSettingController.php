<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\LeadSetting;
use App\Models\LeadCategory;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\User;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HotelManagement\Entities\Facility;
use Modules\HotelManagement\Entities\Floor;
use Modules\HotelManagement\Entities\HMBookingSource;
use Modules\HotelManagement\Entities\RoomType;
use Modules\HotelManagement\Entities\Service;
use Modules\HotelManagement\Entities\BookingType;

class HotelManagementSettingController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'hotel_managment_settings';

        // $this->middleware(function ($request, $next) {
        //     abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

        //     return $next($request);
        // });
    }
    public function index()
    {

        $this->hmfloor = Floor::all();
        $this->RoomTypes = RoomType::all();
        $this->facilities = Facility::all();
        $this->services = Service::all();
        $this->hmbookingsources = HMBookingSource::all();
        $this->bookingtypes = BookingType::all();
        $this->pageTitle = 'hotelmanagement::app.menu.hotelmanagment';

        $tab = request('tab');

        $this->view = match ($tab) {
            'hmbookingsource' => 'hotelmanagement::hotelmanagement-settings.ajax.hmbookingsource',
            'bookingtype' => 'hotelmanagement::hotelmanagement-settings.ajax.bookingtype',
            'roomtypes' => 'hotelmanagement::hotelmanagement-settings.ajax.roomtypes',
            'facilities' => 'hotelmanagement::hotelmanagement-settings.ajax.facilities',
            'services' => 'hotelmanagement::hotelmanagement-settings.ajax.services',
            default => 'hotelmanagement::hotelmanagement-settings.ajax.floor',
        };

        $this->activeTab = $tab ?: 'floor';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('hotelmanagement::hotelmanagement-settings.index', $this->data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hotelmanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
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
    public function edit($id)
    {
        return view('hotelmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
