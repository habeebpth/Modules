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

class HotelManagementFloorSettingController extends AccountBaseController
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
        $floor = Floor::findOrFail($request->floor_id);
        $floor->disable = $request->disable;
        $floor->save();

        return response()->json(['status' => 'success', 'message' => __('Floor status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.floor.create-floor-modal', [
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
            'floor_name'   => 'required|string|max:255',
            'floor_number' => 'required|integer|min:1',
            'company_id'   => 'required|integer|exists:companies,id',
        ]);

        $floor = new Floor();
        $floor->floor_name = $request->floor_name;
        $floor->floor_number = $request->floor_number;
        $floor->disable = 'y';
        $floor->company_id = $request->company_id;
        $floor->save();
        $leadSource = Floor::get();
        $options = BaseModel::options($leadSource);
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
        $this->floor = Floor::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.floor.edit-floor-modal', $this->data);
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
            'floor_name'   => 'required|string|max:255',
            'floor_number' => 'required|integer|min:1',
        ]);
        $floor = Floor::findOrFail($id);

        $floor->floor_name = $request->floor_name;
        $floor->floor_number = $request->floor_number;
        $floor->disable = 'y';
        // $floor->company_id = $request->company_id;
        $floor->save();

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

        Floor::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
