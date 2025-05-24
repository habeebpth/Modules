<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use App\Helper\Files;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HotelManagement\Entities\HMGuests;
use Modules\HotelManagement\DataTables\HmCheckinDataTable;
use Modules\HotelManagement\Entities\BookingType;
use Modules\HotelManagement\Entities\CheckinGuest;
use Modules\HotelManagement\Entities\CheckinRoom;
use Modules\HotelManagement\Entities\HMBookingSource;
use Modules\HotelManagement\Entities\HmCheckin;
use Modules\HotelManagement\Entities\HmRoom;
use Modules\HotelManagement\Entities\RoomType;

class HMCheckinController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.hmcheckin';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    // public function fetchGuests()
    // {
    //     $guests = HMGuests::select('first_name as name', 'phone as mobile', 'id_type as idType', 'id_number as idNumber', 'hashname', 'country_phonecode')->get();
    //     return response()->json($guests);
    // }
    // Fetch Room Types
    public function getRoomTypes()
    {
        $query = request('search');
        $roomTypes = RoomType::where('room_type_name', 'LIKE', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'room_type_name as text']);

        return response()->json($roomTypes);
    }

    // Fetch Room Numbers
    public function getRoomNumbers()
    {
        $query = request('search');
        $rooms = HmRoom::where('room_no', 'LIKE', '%'.$query.'%')
            ->limit(10)
            ->get(['id', 'room_no as text']);

        return response()->json($rooms);
    }

    public function index(HmCheckinDataTable $dataTable)
    {
        $this->hmcheckin = HmCheckin::get();
        return $dataTable->render('hotelmanagement::hm-checkin.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // $this->pageTitle = __('app.addGuest');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        $this->guests = HMGuests::all();
        $this->roomtypes = RoomType::get();
        $this->roomnos = HmRoom::get();
        $this->bookingsources = HMBookingSource::get();
        $this->bookingtypes = BookingType::get();
        $this->view = 'hotelmanagement::hm-checkin.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-checkin.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */

    // public function store(Request $request)
    // {
    //     dd($request->all());

    //     // Validate the request
    //     $request->validate([
    //         'check_in' => 'required|',
    //         'check_out' => 'required|',
    //         // 'guest_id' => 'required|array',
    //         // 'guest_id.*' => 'exists:hm_guests,id',
    //         'room_type_id' => 'required|',
    //         'room_type_id.*' => 'exists:room_types,id',
    //         'room_id' => 'required|array',
    //         'room_id.*' => 'exists:hm_rooms,id',
    //         'adults' => 'nullable|',
    //         'children' => 'nullable|',
    //         'extra_bed' => 'nullable|',
    //         'rent' => 'required|',
    //         'rent.*' => 'numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $check_in_date = is_array($request->check_in) ? $request->check_in[0] : $request->check_in;
    //         $check_out_date = is_array($request->check_out) ? $request->check_out[0] : $request->check_out;
    //         // Create Checkin record
    //         $checkin = new HmCheckin();
    //         $checkin->check_in = Carbon::parse($check_in_date)->format('Y-m-d');
    //         $checkin->check_out = Carbon::parse($check_out_date)->format('Y-m-d');
    //         $checkin->arrival_from = $request->arrival_from;
    //         $checkin->booking_type_id = $request->booking_type_id;
    //         $checkin->booking_reference_id = $request->booking_reference_id;
    //         $checkin->booking_reference_no = $request->booking_reference_no;
    //         $checkin->purpose_of_visit = $request->purpose_of_visit;
    //         $checkin->remarks = $request->remarks;
    //         $checkin->company_id = $request->company_id;
    //         $checkin->save();

    //         // // Loop through rooms and store data
    //         // foreach ($request->rooms as $room) {

    //         //     $checkin_date = is_array($request->checkin) ? $request->checkin[0] : $request->checkin;
    //         //     $checkout_date = is_array($request->checkout) ? $request->checkout[0] : $request->checkout;
    //         //     $checkinRoom = new CheckinRoom();
    //         //     $checkinRoom->checkin_id = $checkin->id;
    //         //     $checkinRoom->room_type_id = $request->room_type_id ?? null;
    //         //     $checkinRoom->checkin = Carbon::parse($checkin_date)->format('Y-m-d');
    //         //     $checkinRoom->checkout = Carbon::parse($checkout_date)->format('Y-m-d');
    //         //     $checkinRoom->room_id = $room;
    //         //     $checkinRoom->adults = $request->adults ?? 0;
    //         //     $checkinRoom->children = $request->children ?? 0;
    //         //     $checkinRoom->extra_bed = $request->extra_bed ?? 0;
    //         //     $checkinRoom->rent = $request->rent ?? 0;
    //         //     $checkinRoom->save();
    //         //     // dd($room->guestid);
    //         //     dd($request->all());
    //         //     if (!empty($request->guest_id)) {
    //         //         foreach ($request->guest_id as $guest_id) {
    //         //             $checkinGuest = new CheckinGuest();
    //         //             $checkinGuest->checkin_id = $checkin->id;
    //         //             $checkinGuest->checkin_room = $checkinRoom->id;
    //         //             $checkinGuest->room_id = $room;
    //         //             $checkinGuest->guest_id = $guest_id;
    //         //             $checkinGuest->save();
    //         //         }
    //         //     }
    //         // }

    //         foreach ($request->rooms as $room) {
    //             $checkinRoom = new CheckinRoom();
    //             $checkinRoom->checkin_id = $checkin->id;
    //             $checkinRoom->room_type_id = $room['room_type_id'];
    //             $checkinRoom->room_id = $room['room_id'];
    //             $checkinRoom->adults = $room['adults'] ?? 0;
    //             $checkinRoom->children = $room['children'] ?? 0;
    //             $checkinRoom->extra_bed = $room['extra_bed'] ?? 0;
    //             $checkinRoom->rent = $room['rent'];
    //             $checkinRoom->checkin = Carbon::parse($request->check_in)->format('Y-m-d');
    //             $checkinRoom->checkout = Carbon::parse($request->check_out)->format('Y-m-d');
    //             $checkinRoom->save();

    //             // Handle guests in the room
    //             if (!empty($room['guestid'])) {
    //                 foreach ($room['guestid'] as $guest_id) {
    //                     $checkinGuest = new CheckinGuest();
    //                     $checkinGuest->checkin_id = $checkin->id;
    //                     $checkinGuest->checkin_room_id = $checkinRoom->id;
    //                     $checkinGuest->room_id = $room['room_id'];
    //                     $checkinGuest->guest_id = $guest_id;
    //                     $checkinGuest->save();
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return Reply::successWithData(__('messages.GuestAddedSuccessfully'), [
    //             'redirectUrl' => route('hm-checkin.index')
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return Reply::error(__('messages.SomethingWentWrong') . ' ' . $e->getMessage());
    //     }
    // }
    public function fetchGuests(Request $request)
    {
        $search = $request->input('search');

        $guests = HMGuests::where('first_name', 'LIKE', "%{$search}%")
            ->orWhere('phone', 'LIKE', "%{$search}%")
            ->select('id', 'first_name as name', 'phone as mobile', 'id_type as idType', 'id_number as idNumber', 'hashname', 'country_phonecode')
            ->get();

        return response()->json($guests);
    }


    public function searchGuests(Request $request)
    {
        $search = $request->input('search');

        $guest = HMGuests::where('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($guest);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the request
        $request->validate([
            'check_in' => 'required|date_format:'.company()->date_format,
            'check_out' => 'required|date_format:'.company()->date_format.'|after_or_equal:check_in',
            'room_type_id' => 'required|array',
            'room_type_id.*' => 'exists:room_types,id',
            'room_id' => 'required|array',
            'room_id.*' => 'exists:hm_rooms,id',
            'adults' => 'nullable|array',
            'adults.*' => 'numeric|min:0',
            'children' => 'nullable|array',
            'children.*' => 'numeric|min:0',
            'extra_bed' => 'nullable|array',
            'extra_bed.*' => 'numeric|min:0',
            'rent' => 'required|array',
            'rent.*' => 'numeric|min:0',
            'checkin.*' => 'date_format:'.company()->date_format,
            'checkout.*' => 'date_format:'.company()->date_format,
            // 'guest_name' => 'required|array',
            // 'guest_name.*' => 'array', // Each index should be an array
            // 'guest_name.*.*' => 'string|max:255', // Each value inside should be a string
        ]);

        DB::beginTransaction();
        try {
            // Parse check-in and check-out dates
            $check_in_date = Carbon::createFromFormat('d-m-Y', $request->check_in)->format('Y-m-d');
            $check_out_date = Carbon::createFromFormat('d-m-Y', $request->check_out)->format('Y-m-d');

            // Create Checkin record
            $checkin = new HmCheckin();
            $checkin->check_in = $check_in_date;
            $checkin->check_out = $check_out_date;
            $checkin->arrival_from = $request->arrival_from;
            $checkin->booking_type_id = $request->booking_type_id;
            $checkin->booking_reference_id = $request->booking_reference_id;
            $checkin->booking_reference_no = $request->booking_reference_no;
            $checkin->purpose_of_visit = $request->purpose_of_visit;
            $checkin->remarks = $request->remarks;
            $checkin->company_id = $request->company_id;
            $checkin->save();

            // Loop through rooms and store data
            foreach ($request->room_id as $index => $room_id) {
                $checkin_date = Carbon::createFromFormat('d-m-Y', $request->checkin[$index])->format('Y-m-d');
                $checkout_date = Carbon::createFromFormat('d-m-Y', $request->checkout[$index])->format('Y-m-d');

                $checkinRoom = new CheckinRoom();
                $checkinRoom->checkin_id = $checkin->id;
                $checkinRoom->room_type_id = $request->room_type_id[$index] ?? null;
                $checkinRoom->room_id = $room_id;
                $checkinRoom->adults = $request->adults[$index] ?? 0;
                $checkinRoom->children = $request->children[$index] ?? 0;
                $checkinRoom->extra_bed = $request->extra_bed[$index] ?? 0;
                $checkinRoom->rent = $request->rent[$index] ?? 0;
                $checkinRoom->checkin = $checkin_date;
                $checkinRoom->checkout = $checkout_date;
                $checkinRoom->save();
                // dd($request->guest_name[$index]);

                if (isset($request->guest_id[$index]) && is_array($request->guest_id[$index])) {
                    foreach ($request->guest_id[$index] as $guestIndex => $guestId) {
                        if (! empty($guestId)) {
                            $checkinGuest = new CheckinGuest();
                            $checkinGuest->guest_id = (int) $guestId;
                            $checkinGuest->checkin_id = $checkin->id;
                            $checkinGuest->checkin_room_id = $checkinRoom->id;
                            $checkinGuest->room_id = $room_id;
                            $checkinGuest->save();
                        }
                    }
                }
            }


            DB::commit();
            return Reply::successWithData(__('messages.GuestAddedSuccessfully'), [
                'redirectUrl' => route('hm-checkin.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error(__('messages.SomethingWentWrong').' '.$e->getMessage());
        }
    }



    //     /**
    //      * Show the specified resource.
    //      */
    //     public function show($id)
    //     {
    //         return view('hotelmanagement::show');
    //     }

    //     /**
    //      * Show the form for editing the specified resource.
    //      */
    //     // public function edit($id)
    //     // {
    //     //     return view('hotelmanagement::edit');
    //     // }

    public function edit($id)
    {
        $this->pageTitle = __('app.editGuest');

        $this->checkin = HmCheckin::with('rooms.guests')->findOrFail($id);
        $this->roomtypes = RoomType::all();
        $this->roomnos = HmRoom::all();
        $this->bookingtypes = BookingType::all();
        $this->bookingsources = HMBookingSource::all();
        $this->guests = HMGuests::all();

        $this->view = 'hotelmanagement::hm-checkin.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-checkin.create', $this->data);
    }

    //     /**
    //      * Update the specified resource in storage.
    //      */
    // public function update(Request $request, $id)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'check_in' => 'required|date',
    //         'check_out' => 'required|date|after_or_equal:check_in',
    //         'guest_id' => 'required|array',
    //         'guest_id.*' => 'exists:hm_guests,id',
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'room_id' => 'required|array',
    //         'room_id.*' => 'exists:hm_rooms,id',
    //         'adults' => 'nullable|integer|min:0',
    //         'children' => 'nullable|integer|min:0',
    //         'extra_bed' => 'nullable|integer|min:0',
    //         'rent' => 'required|numeric|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $checkin = HmCheckin::findOrFail($id);

    //         if ($request->has('deleted_rooms')) {
    //             foreach ($request->input('deleted_rooms') as $roomId) {
    //                 CheckinRoom::where('id', $roomId)->delete();
    //             }
    //         }
    //         $checkin->check_in = Carbon::parse($request->check_in)->format('Y-m-d');
    //         $checkin->check_out = Carbon::parse($request->check_out)->format('Y-m-d');
    //         $checkin->arrival_from = $request->arrival_from;
    //         $checkin->booking_type_id = $request->booking_type_id;
    //         $checkin->booking_reference_id = $request->booking_reference_id;
    //         $checkin->booking_reference_no = $request->booking_reference_no;
    //         $checkin->purpose_of_visit = $request->purpose_of_visit;
    //         $checkin->remarks = $request->remarks;
    //         $checkin->save();

    //         // Remove existing check-in rooms and guests
    //         CheckinRoom::where('checkin_id', $id)->delete();
    //         CheckinGuest::where('checkin_id', $id)->delete();

    //         // Loop through rooms and store updated data
    //         foreach ($request->room_id as $room_id) {
    //             $checkinRoom = new CheckinRoom();
    //             $checkinRoom->checkin_id = $checkin->id;
    //             $checkinRoom->room_type_id = $request->room_type_id;
    //             $checkinRoom->checkin = $checkin->check_in;
    //             $checkinRoom->checkout = $checkin->check_out;
    //             $checkinRoom->room_id = $room_id;
    //             $checkinRoom->adults = $request->adults ?? 0;
    //             $checkinRoom->children = $request->children ?? 0;
    //             $checkinRoom->extra_bed = $request->extra_bed ?? 0;
    //             $checkinRoom->rent = $request->rent;
    //             $checkinRoom->save();

    //             foreach ($request->guest_id as $guest_id) {
    //                 $checkinGuest = new CheckinGuest();
    //                 $checkinGuest->checkin_id = $checkin->id;
    //                 $checkinGuest->checkin_room_id = $checkinRoom->id;
    //                 $checkinGuest->room_id = $room_id;
    //                 $checkinGuest->guest_id = $guest_id;
    //                 $checkinGuest->save();
    //             }
    //         }

    //         DB::commit();
    //         return Reply::successWithData(__('messages.CheckinUpdatedSuccessfully'), [
    //             'redirectUrl' => route('hm-checkin.index')
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return Reply::error(__('messages.SomethingWentWrong') . ' ' . $e->getMessage());
    //     }
    // }



    public function update(Request $request, $id)
    {
        try {
            // Retrieve existing check-in record
            $checkin = HmCheckin::findOrFail($id);

            // Ensure check-in and check-out dates are properly formatted
            $check_in_date = ! empty($request->check_in) ? Carbon::parse($request->check_in)->format('Y-m-d') : null;
            $check_out_date = ! empty($request->check_out) ? Carbon::parse($request->check_out)->format('Y-m-d') : null;

            // Update check-in details
            $checkin->update([
                'check_in' => $check_in_date,
                'check_out' => $check_out_date,
                'arrival_from' => $request->arrival_from,
                'booking_type_id' => $request->booking_type_id,
                'booking_reference_id' => $request->booking_reference_id,
                'booking_reference_no' => $request->booking_reference_no,
                'purpose_of_visit' => $request->purpose_of_visit,
                'remarks' => $request->remarks,
            ]);

            // Handle deleted rooms
            if (! empty($request->deleted_rooms)) {
                CheckinRoom::whereIn('id', $request->deleted_rooms)->delete();
            }

            // Update or insert rooms
            if (! empty($request->room_id)) {
                foreach ($request->room_id as $index => $room_id) {
                    $checkin_date = Carbon::createFromFormat(companyOrGlobalSetting()->date_format, $request->checkin[$index])->format('Y-m-d');
                $checkout_date = Carbon::createFromFormat(companyOrGlobalSetting()->date_format, $request->checkout[$index])->format('Y-m-d');
                    // dd($room['room_id']);
                    // Update or create room record
                    $checkinRoom = CheckinRoom::updateOrCreate(
                        [
                            'id' => $request->checkin_room_id[$index] ?? 0,
                        ],
                        [
                            'room_id' => $room_id, // Update room_id
                            'room_type_id' =>  $request->room_type_id[$index] ?? null,
                            'adults' => $request->adults[$index] ?? 0,
                            'children' => $request->children[$index] ?? 0,
                            'extra_bed' => $request->extra_bed[$index] ?? 0,
                            'checkin' => $checkin_date,
                            'checkout' => $checkout_date,
                            'rent' => $request->rent[$index] ?? 0,
                        ]
                    );

                    // Handle guests for each room
                    if (isset($request->guest_id[$index]) && is_array($request->guest_id[$index])) {
                        foreach ($request->guest_id[$index] as $guestIndex => $guestId) {
                            CheckinGuest::updateOrCreate(
                                [
                                    'checkin_room_id' => $checkinRoom->id,
                                    'guest_id' => $guestId, // Use the actual guest ID
                                ],
                                [
                                    'guest_id' => $guestId, // Correcting this part
                                    'checkin_id' => $checkin->id,
                                    'room_id' => $room_id,
                                ]
                            );
                        }
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Check-in updated successfully',
                'redirectUrl' => route('hm-checkin.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }



    //     /**
    //      * Remove the specified resource from storage.
    //      */
    //     public function destroy($id)
    //     {

    //         $hmguests = HMGuests::findOrFail($id);
    //         $hmguests->delete();

    //         return Reply::successWithData(__('messages.GuestDeletedSuccessfully'),['redirectUrl' => route('hm-guests.index')] );
    //     }
}
