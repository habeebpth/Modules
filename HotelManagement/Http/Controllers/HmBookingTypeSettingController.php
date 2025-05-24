<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\BookingType;

class HmBookingTypeSettingController extends AccountBaseController
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
        $bookingtype = BookingType::findOrFail($request->bookingtype_id);
        $bookingtype->disable = $request->disable;
        $bookingtype->save();

        return response()->json(['status' => 'success', 'message' => __('BookingType status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.bookingtype.create-bookingtype-modal', $this->data);

    }

    /**
     * @param StoreLeadSource $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'company_id'   => 'required|integer|exists:companies,id',
        ]);

        $bookingtype = new BookingType();
        $bookingtype->name = $request->name;
        $bookingtype->disable = 'y';
        $bookingtype->company_id = $request->company_id;
        $bookingtype->save();
        $bookingtypes = BookingType::get();
        $options = BaseModel::options($bookingtypes);
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
        $this->bookingtype = BookingType::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.bookingtype.edit-bookingtype-modal', $this->data);
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
            'name'   => 'required|string|max:255',
        ]);
        $bookingtype = BookingType::findOrFail($id);

        $bookingtype->name = $request->name;
        $bookingtype->disable = 'y';
        // $bookingtype->company_id = $request->company_id;
        $bookingtype->save();

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

        BookingType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
