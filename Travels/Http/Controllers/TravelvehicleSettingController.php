<?php

namespace Modules\Travels\Http\Controllers;

use App\Helper\Reply;
use App\Helper\Files;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Modules\Travels\Entities\Destination;
use Modules\Travels\Entities\Vehicle;
use Modules\Travels\Entities\VehicleType;

class TravelvehicleSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.businessAddresses';
        $this->activeSettingMenu = 'business_address';
        $this->middleware(function ($request, $next) {
            abort_403(user()->permission('manage_company_setting') !== 'all');

            return $next($request);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function updateStatus(Request $request)
    {
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $vehicle->disable = $request->disable;
        $vehicle->save();

        return response()->json(['status' => 'success', 'message' => __('vehicle status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        $this->vehicleTypes = VehicleType::all();
        $this->countries = countries();
        return view('travels::travel-settings.vehicle.create-vehicle-modal', $this->data);

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number',
            'vehicle_code' => 'required|string|max:50|unique:vehicles,vehicle_code',
            'no_of_seats' => 'required|integer|min:1',
            'country_id' => 'required|integer|max:255',
            'company_id' => 'required|integer|exists:companies,id',

        ]);
        $vehicle = new Vehicle();
        $vehicle->name = $request->name;
        $vehicle->vehicle_type_id = $request->vehicle_type_id;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->vehicle_code = $request->vehicle_code;
        $vehicle->no_of_seats = $request->no_of_seats;
        $vehicle->company_id = $request->company_id;
        $vehicle->country_id = $request->country_id;
        $vehicle->disable = 'y';
        $vehicle->save();

        $vehicles = Vehicle::all();
        $options = BaseModel::options($vehicles);

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $options]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $this->vehicleTypes = VehicleType::all();
        $this->countries = countries();
        $this->vehicle = Vehicle::findOrFail($id);
        return view('travels::travel-settings.vehicle.edit-vehicle-modal', $this->data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $id, // Using $id for uniqueness check
            'vehicle_code' => 'required|string|max:50|unique:vehicles,vehicle_code,' . $id, // Using $id for uniqueness check
            'no_of_seats' => 'required|integer|min:1',
            'country_id' => 'required|integer|max:255',
        ]);

        // Find the vehicle record
        $vehicle = Vehicle::findOrFail($id);

        // Update the vehicle data
        $vehicle->name = $request->name;
        $vehicle->vehicle_type_id = $request->vehicle_type_id;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->vehicle_code = $request->vehicle_code;
        $vehicle->no_of_seats = $request->no_of_seats;
        $vehicle->country_id = $request->country_id;
        $vehicle->disable = 'y';

        // Save the updated vehicle data
        $vehicle->save();

        // Return a success response
        return Reply::success(__('messages.updateSuccess'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Vehicle::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
