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
use Modules\Travels\Entities\VehicleType;

class TravelvehicletypeSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('leads', $this->user->modules));
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
        $vehicletype = VehicleType::findOrFail($request->vehicletype_id);
        $vehicletype->disable = $request->disable;
        $vehicletype->save();

        return response()->json(['status' => 'success', 'message' => __('vehicletype status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('travels::travel-settings.vehicletype.create-vehicletype-modal', [
            'company_id' => $this->company_id
        ]);

    }

    public function store(Request $request)
    {

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'no_of_seats' => 'required|integer|min:1',
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        $vehicletype = new VehicleType();
        $vehicletype->name = $request->name;
        $vehicletype->no_of_seats = $request->no_of_seats;
        $vehicletype->company_id = $request->company_id;
        $vehicletype->disable = 'y';
        $vehicletype->save();

        $vehicletypes = VehicleType::all();
        $options = BaseModel::options($vehicletypes);

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
        $this->vehicletype = VehicleType::findOrFail($id);
        return view('travels::travel-settings.vehicletype.edit-vehicletype-modal', $this->data);
    }


    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'no_of_seats' => 'required|integer|min:1',
        ]);

        // Find the vehicle type by ID
        $vehicletype = VehicleType::findOrFail($id);

        // Update the fields
        $vehicletype->name = $request->name;
        $vehicletype->no_of_seats = $request->no_of_seats;
        $vehicletype->disable = 'y';
        $vehicletype->save();

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

        VehicleType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
