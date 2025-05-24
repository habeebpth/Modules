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
use Modules\HotelManagement\Entities\Service;

class HmServiceSettingController extends AccountBaseController
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
        $Service = Service::findOrFail($request->service_id);
        $Service->disable = $request->disable;
        $Service->save();

        return response()->json(['status' => 'success', 'message' => __('Service status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.services.create-services-modal', [
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
        // Validate the incoming request data
        $request->validate([
            'service_name' => 'required|string|max:255',
            'base_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:1000',
            'company_id'   => 'required|integer|exists:companies,id',
        ]);

        // Save the service
        $Service = new Service();
        $Service->service_name = $request->service_name;
        $Service->base_price = $request->base_price;
        $Service->description = $request->description;
        $Service->disable = 'y';
        $Service->company_id = $request->company_id;
        $Service->save();

        // Retrieve updated options
        $HMService = Service::get();
        $options = BaseModel::options($HMService);

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
        $this->service = Service::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.services.edit-services-modal', $this->data);
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
            'service_name' => 'required|string|max:255',
            'base_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:1000',
        ]);
        $Service = Service::findOrFail($id);

        $Service->service_name = $request->service_name;
        $Service->base_price = $request->base_price;
        $Service->description = $request->description;
        $Service->disable = 'y';
        $Service->save();

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

        Service::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
