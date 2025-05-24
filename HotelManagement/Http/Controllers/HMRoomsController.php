<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HotelManagement\Entities\HmRoom;
use Modules\HotelManagement\Entities\Facility;
use Modules\HotelManagement\Entities\RoomType;
use Modules\HotelManagement\Entities\Property;
use Modules\HotelManagement\Entities\Floor;
use Modules\HotelManagement\DataTables\HMRoomDataTable;
use Modules\HotelManagement\Entities\RoomFacility;

class HMRoomsController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.hmRooms';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('hotelmanagement', $this->user->modules));

            return $next($request);
        });
    }
    public function index(HMRoomDataTable $dataTable)
    {
        $this->hmrooms = HmRoom::get();
        return $dataTable->render('hotelmanagement::hm-rooms.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.addRoom');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        $this->properties = Property::get();
        $this->roomTypes = RoomType::get();
        $this->facilities = Facility::get();
        $this->floors = Floor::get();
        if (request()->has('default_assign') && request('default_assign') != '') {
            $this->defaultAssignee = request('default_assign');
        }
        $this->userData  = [];
        // $this->leads = Lead::allLeads();
        $this->view = 'hotelmanagement::hm-rooms.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-rooms.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate form data
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'property_id' => 'required|integer|exists:properties,id',
            'floor_id' => 'required|integer|exists:floors,id',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'facilities_id' => 'required|array|exists:facilities,id', // Ensure it's an array
            'room_no' => 'required|string|max:50|unique:hm_rooms,room_no',
            'room_size' => 'required|numeric|min:0',
            'no_of_beds' => 'required|integer|min:1',
            'room_description' => 'nullable|string',
            'room_conditions' => 'nullable|string',
        ]);

        // Store the room data
        $room = new HmRoom();
        $room->company_id = $request->company_id;
        $room->property_id = $request->property_id;
        $room->floor_id = $request->floor_id;
        $room->room_type_id = $request->room_type_id;
        $room->room_no = $request->room_no;
        $room->room_size = $request->room_size;
        $room->no_of_beds = $request->no_of_beds;
        $room->room_description = $request->room_description;
        $room->room_conditions = $request->room_conditions;
        $room->save();

        if ($request->has('facilities_id')) {
            foreach ($request->facilities_id as $facilityId) {
                RoomFacility::create([
                    'room_id' => $room->id, // assuming $room is the saved room object
                    'facility_id' => $facilityId,
                ]);
            }
        }

    // Return success response
    return Reply::successWithData(__('messages.roomAddedSuccessfully'), [
        'redirectUrl' => route('hm-rooms.index')
    ]);
}



    /**
     * Show the specified resource.
     */

    public function show($id)
    {
        $this->hmrooms = HmRoom::with('files')->findOrFail($id);

        $tab = request('tab');

        switch ($tab) {

            case 'files':
                $this->view = 'hotelmanagement::hm-rooms.ajax.files';
                break;
            default:
                $this->view = 'hotelmanagement::hm-rooms.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('hotelmanagement::hm-rooms.show', $this->data);

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
        $this->pageTitle = __('app.editRoom');

        $this->room = HmRoom::findOrFail($id);
        $this->properties = Property::get();
        $this->roomTypes = RoomType::get();
        $this->facilities = Facility::get();
        $this->floors = Floor::get();
        $this->countries = countries();
        $this->roomFacilities = $this->room->facilities()->pluck('facility_id')->toArray();

        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'hotelmanagement::hm-rooms.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-rooms.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'floor_id' => 'required|integer|exists:floors,id',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'facilities_id' => 'required|array',
            'room_no' => 'required|string|max:50|unique:hm_rooms,room_no,' . $id,
            'room_size' => 'required|numeric|min:0',
            'no_of_beds' => 'required|integer|min:1',
            'room_description' => 'nullable|string',
            'room_conditions' => 'nullable|string',
        ]);

        // Find the room
        $room = HmRoom::findOrFail($id);

        // Update room data
        $room->property_id = $request->property_id;
        $room->floor_id = $request->floor_id;
        $room->room_type_id = $request->room_type_id;
        $room->room_no = $request->room_no;
        $room->room_size = $request->room_size;
        $room->no_of_beds = $request->no_of_beds;
        $room->room_description = $request->room_description;
        $room->room_conditions = $request->room_conditions;

        // Save the room
        $room->save();

    // Sync facilities: Delete old ones and attach new ones
    $room->facilities()->delete();  // Remove existing facilities
    foreach ($request->facilities_id as $facilityId) {
        // Attach each facility to the room
        $room->facilities()->create(['facility_id' => $facilityId]);
    }

    return Reply::successWithData(__('messages.roomUpdatedSuccessfully'), [
        'redirectUrl' => route('hm-rooms.index')
    ]);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // dd('1234');

        $hmroom = HmRoom::findOrFail($id);
        $hmroom->delete();

        return Reply::successWithData(__('messages.roomDeletedSuccessfully'), ['redirectUrl' => route('hm-rooms.index')]);
    }
}
