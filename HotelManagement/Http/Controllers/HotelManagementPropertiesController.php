<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HotelManagement\Entities\Property;
use Modules\HotelManagement\DataTables\HmPropertiesDataTable;
use Modules\HotelManagement\DataTables\HMRoomDataTable;

class HotelManagementPropertiesController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.HMProperties';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));
            abort_403(!in_array('hotelmanagement', $this->user->modules));

            return $next($request);
        });
    }
    public function index(HmPropertiesDataTable $dataTable)
    {
        $this->Properties = Property::get();
        return $dataTable->render('hotelmanagement::hm-properties.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.addProperty');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        // $this->leads = Lead::allLeads();
        $this->view = 'hotelmanagement::hm-properties.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-properties.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'property_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country_id' => 'required|integer',
            'zip_code' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:100',
            'location' => 'nullable|string|max:100',
        ]);

        $property = new Property();
        $property->company_id = $request->company_id;
        $property->property_name = $request->property_name;
        $property->address = $request->address;
        $property->city = $request->city;
        $property->state = $request->state;
        $property->country_id = $request->country_id;
        $property->zip_code = $request->zip_code;
        $property->contact_number = $request->contact_number;
        $property->email = $request->email;
        $property->website = $request->website;
        $property->location = $request->location;
        $property->save();
        return Reply::successWithData(__('messages.propertyAddedSuccessfully'), ['redirectUrl' => route('hm-properties.index')]);
    }

    /**
     * Show the specified resource.
     */
    // public function show($id)
    // {
    //     return view('hotelmanagement::show');
    // }

    public function show($id)
    {
        $this->property = Property::with('files')->findOrFail($id);

        $tab = request('tab');

        switch ($tab) {
            case 'rooms':
                return $this->Room();
            case 'files':
                $this->view = 'hotelmanagement::hm-properties.ajax.files';
                break;
            default:
                $this->view = 'hotelmanagement::hm-properties.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('hotelmanagement::hm-properties.show', $this->data);
    }
    public function Room()
    {
        // $id = request()->segment(3);
        $tab = request('tab');

        $this->activeTab = $tab ?: 'overview';
        // $this->attendancetypes = AttendanceType::all();
        // $this->geogroup = EmployeeGeoGroup::where('employee_id', $id)->get();
        // $this->employeedetails = EmployeeDetails::where('user_id', $id)->first();
        $this->view = 'hotelmanagement::hm-properties.ajax.room';
        $dataTable = new HMRoomDataTable();

        return $dataTable->render('hotelmanagement::hm-properties.show', $this->data);
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
        $this->pageTitle = __('app.editProperty');

        $this->property = Property::findOrFail($id);
        $this->countries = countries();
        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'hotelmanagement::hm-properties.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-properties.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'property_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country_id' => 'required|integer',
            'zip_code' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:100',
            'location' => 'nullable|string|max:100',
        ]);

        $property = Property::findOrFail($id);
        $property->property_name = $request->property_name;
        $property->address = $request->address;
        $property->city = $request->city;
        $property->state = $request->state;
        $property->country_id = $request->country_id;
        $property->zip_code = $request->zip_code;
        $property->contact_number = $request->contact_number;
        $property->email = $request->email;
        $property->website = $request->website;
        $property->location = $request->location;
        $property->save();

        return Reply::successWithData(__('messages.propertyUpdatedSuccessfully'), ['redirectUrl' => route('hm-properties.index')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $Property = Property::findOrFail($id);
        $Property->delete();

        return Reply::successWithData(__('messages.propertyDeletedSuccessfully'), ['redirectUrl' => route('hm-properties.index')]);
    }
}
