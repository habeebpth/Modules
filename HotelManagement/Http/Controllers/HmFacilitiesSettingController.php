<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\Facility;
use Modules\HotelManagement\Entities\Floor;
use Modules\HotelManagement\Entities\RoomType;

class HmFacilitiesSettingController extends AccountBaseController
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
        $Facility = Facility::findOrFail($request->facilities_id);
        $Facility->disable = $request->disable;
        $Facility->save();

        return response()->json(['status' => 'success', 'message' => __('Facility status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.facilities.create-facilities-modal', [
            'company_id' => $this->company_id
        ]);

    }

    /**
     * @param StoreLeadSource $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'company_id'    => 'required|integer|exists:companies,id',
        ]);

        $Facility = new Facility();
        $Facility->facility_name = $request->facility_name;
        $Facility->description = $request->description;
        $Facility->disable = 'y';
        $Facility->company_id = $request->company_id;
        $Facility->save();
        $HMFacility = Facility::get();
        $options = BaseModel::options($HMFacility);
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
        $this->Facility = Facility::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.facilities.edit-facilities-modal', $this->data);
    }

    /**
     * @param UpdateLeadSource $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
        ]);
        $Facility = Facility::findOrFail($id);

        $Facility->facility_name = $request->facility_name;
        $Facility->description = $request->description;
        $Facility->disable = 'y';
        $Facility->save();

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

        Facility::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
