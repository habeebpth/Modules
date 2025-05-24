<?php

namespace Modules\DWC\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DWC\DataTables\DwcHorseDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\Salutation;
use Modules\DWC\Entities\DwcHotel;
use App\Http\Controllers\AccountBaseController;
use Modules\DWC\Entities\DwcHorse;
use Modules\DWC\Entities\DwcGuest;
use Modules\DWC\Entities\DwcHotelRoomType;
use App\Helper\Reply;
use DB;
use Modules\DWC\Entities\DwcFlightTicket;
use Modules\DWC\Entities\DWCHorse as EntitiesDWCHorse;
use Modules\DWC\Entities\DwcHotelReservation;

class DwcHorseController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.Horses';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */

    public function index(DwcHorseDataTable $dataTable)
    {
        if (!request()->ajax()) {
            $this->DwcHorse = DwcHorse::all();
            // $this->skills = Skill::all();
        }

        // dd($this->data);

        return $dataTable->render('dwc::horse.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.Hotel');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        $this->salutations = Salutation::cases();
        // $this->leads = Lead::allLeads();
        $this->guests = DwcGuest::all();
        $this->horses = DwcHorse::all();
        $this->view = 'dwc::horse.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('dwc::horse.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'horse' => 'required|integer|exists:dwc_horses,id',
            'passport_number' => 'nullable|string|max:50|unique:dwc_guests,passport_number',
        ]);

        $guest = new DwcGuest();
        $guest->company_id = company()->id;
        $guest->horse_id = $request->horse;
        $guest->amendment_date = $request->amendment_date;
        $guest->guest_type = $request->guest_type;
        $guest->last_name = $request->last_name;
        $guest->first_name = $request->first_name;
        $guest->title = $request->title;
        $guest->salutation = $request->salutation;
        $guest->company = $request->company;
        $guest->address_1 = $request->address;
        $guest->address_2 = $request->address_2;
        $guest->state = $request->state;
        $guest->zip = $request->zip;
        $guest->country = $request->country;
        $guest->tel = $request->tel;
        $guest->fax = $request->fax;
        $guest->mobile_county_code = $request->country_phonecode;
        $guest->mobile = $request->mobile;
        $guest->email = $request->email;
        $guest->nationality = $request->nationality;
        $guest->visa_required = $request->visa_required;
        $guest->passport_number = $request->passport_number;
        $guest->save();

        if (count($request->flight_no) > 0) {
            foreach ($request->flight_no as $index => $flightNo) {
                $flight = new DwcFlightTicket();
                $flight->flight_no = $flightNo;
                $flight->departure_date = $request->departure_date[$index] ?? null;
                $flight->departure_time = $request->departure_time[$index] ?? null;
                $flight->arrival_date = $request->arrival_date[$index] ?? null;
                $flight->arrival_time = $request->arrival_time[$index] ?? null;
                $flight->flight_from = $request->flight_from[$index];
                $flight->flight_to = $request->flight_to[$index];
                $flight->flight_class = $request->flight_class[$index] ?? null;
                $flight->locator = $request->locator[$index] ?? null;
                $flight->ticket_number = $request->ticket_number[$index] ?? null;
                $flight->note_1 = $request->note_1[$index] ?? null;
                $flight->save();
                if ($flight) {
                    $guest->flightTickets()->attach($flight->id);
                }
            }
        }

        if (count($request->hotel_id) > 0) {
            foreach ($request->hotel_id as $index => $hotel_id) {
                $hotel = new DwcHotelReservation();
                $hotel->hotel_id = $hotel_id;
                $hotel->room_type = $request->room_type[$index] ?? null;
                $hotel->reservation_date = $request->reservation_date[$index] ?? null;
                $hotel->sharing_with = $request->sharing_with[$index] ?? null;
                $hotel->billing_code = $request->billing_code[$index] ?? null;
                $hotel->no_of_nights = $request->no_of_nights[$index];
                $hotel->billing_start_date = $request->billing_start_date[$index];
                $hotel->category = $request->category[$index] ?? null;
                $hotel->sub_category = $request->sub_category[$index] ?? null;
                $hotel->note_2 = $request->note_2[$index] ?? null;
                $hotel->save();
                if ($hotel) {
                    $guest->hotelReservations()->attach($hotel->id);
                }
            }
        }

        return Reply::successWithData(__('messages.hotelAddedSuccessfully'), ['redirectUrl' => route('hotel.index')]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->horses = DwcHorse::findOrFail($id);
        $this->guests = DwcGuest::where('horse_id', $id)->get();
        $tab = request('tab');

        switch ($tab) {

            case 'guest':
                $this->view = 'dwc::horse.ajax.guest';
                break;
            default:
                $this->view = 'dwc::horse.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('dwc::horse.show', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.editHotel');

        $this->hotels = DwcHotel::findOrFail($id);
        $this->countries = countries();
        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'dwc::hotel.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        // return view('dwc::edit');
        return view('dwc::hotel.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'total_rooms' => 'nullable|integer|min:1',
            'price_per_night' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|string',
        ]);

        $hotel = DwcHotel::findOrFail($id);
        $hotel->name = $request->name;
        $hotel->contact_number = $request->contact_number;
        $hotel->email = $request->email;
        $hotel->total_rooms = $request->total_rooms;
        $hotel->price_per_night = $request->price_per_night;
        $hotel->location = $request->location;
        $hotel->amenities = $request->amenities;
        $hotel->save();

        return Reply::successWithData(__('messages.hotelUpdatedSuccessfully'), ['redirectUrl' => route('hotels.index')]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
