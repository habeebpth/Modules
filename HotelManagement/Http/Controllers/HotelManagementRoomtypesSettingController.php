<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\Floor;
use Modules\HotelManagement\Entities\RoomType;

class HotelManagementRoomtypesSettingController extends AccountBaseController
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
        $roomtype = RoomType::findOrFail($request->roomtype_id);
        $roomtype->disable = $request->disable;
        $roomtype->save();

        return response()->json(['status' => 'success', 'message' => __('Room type status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.roomtypes.create-roomtypes-modal', [
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
            'room_type_name' => 'required|string|max:255',
            'base_price'     => 'required|numeric|min:0',
            'max_occupancy'  => 'required|integer|min:1',
            'description'    => 'nullable|string|max:1000',
            'company_id'     => 'required|integer|exists:companies,id',
        ]);


        $RoomType = new RoomType();
        $RoomType->room_type_name = $request->room_type_name;
        $RoomType->base_price = $request->base_price;
        $RoomType->max_occupancy = $request->max_occupancy;
        $RoomType->description = $request->description;
        $RoomType->disable = 'y';
        $RoomType->company_id = $request->company_id;
        $RoomType->save();
        $HMRoomType = RoomType::get();
        $options = BaseModel::options($HMRoomType);
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
        $this->roomtype = RoomType::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.roomtypes.edit-roomtypes-modal', $this->data);
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
            'room_type_name' => 'required|string|max:255',
            'base_price'     => 'required|numeric|min:0',
            'max_occupancy'  => 'required|integer|min:1',
            'description'    => 'nullable|string|max:1000',
        ]);
        $RoomType = RoomType::findOrFail($id);

        $RoomType->room_type_name = $request->room_type_name;
        $RoomType->base_price = $request->base_price;
        $RoomType->max_occupancy = $request->max_occupancy;
        $RoomType->description = $request->description;
        $RoomType->disable = 'y';
        $RoomType->save();

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

        RoomType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
