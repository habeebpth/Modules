<?php

namespace Modules\Travels\Http\Controllers;

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
use Modules\Travels\Entities\VehicleType;
use Modules\Travels\Entities\Airline;
use Modules\Travels\Entities\Destination;
use Modules\Travels\Entities\Vehicle;

class TravelSettingController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'travel_settings';

        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index()
    {

        $this->airlines = Airline::all();
        $this->destinations = Destination::all();
        $this->vehicletypes = VehicleType::all();
        $this->vehicles = Vehicle::with('vehicletype')->get();
        $this->pageTitle = 'travels::app.menu.travels';

        $tab = request('tab');

        $this->view = match ($tab) {
            'destination' => 'travels::travel-settings.ajax.destination',
            'vehicletype' => 'travels::travel-settings.ajax.vehicletype',
            'vehicle' => 'travels::travel-settings.ajax.vehicle',
            default => 'travels::travel-settings.ajax.airline',
        };

        $this->activeTab = $tab ?: 'airline';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('travels::travel-settings.index', $this->data);
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
