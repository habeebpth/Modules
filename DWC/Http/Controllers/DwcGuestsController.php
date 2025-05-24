<?php

namespace Modules\DWC\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DWC\DataTables\ArraivalGuestDataTable;
use Modules\DWC\DataTables\FlightsGuestDataTable;
use Modules\DWC\DataTables\GuestsDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\Salutation;
use Modules\DWC\DataTables\HotelGuestDataTable;
use Modules\DWC\Entities\DwcAirport;
use Modules\DWC\Entities\DwcGuest;
use App\Http\Controllers\AccountBaseController;
use Modules\DWC\Entities\DwcHorse;
use Modules\DWC\Entities\DwcGuestType;
use Modules\DWC\Entities\DwcBillingCode;
use Modules\DWC\Entities\DwcRaces;
use App\Traits\ImportExcel;
use App\Helper\Reply;
use Modules\DWC\Http\Requests\Guest\ImportRequest;
use Modules\DWC\Http\Requests\Guest\ImportProcessRequest;
use DB;
use Modules\DWC\Entities\DwcFlightTicket;
use Modules\DWC\Entities\DWCHorse as EntitiesDWCHorse;
use Modules\DWC\Entities\DwcHotel;
use Modules\DWC\Entities\DwcHotelReservation;
use Modules\DWC\Imports\GuestImport;
use Modules\DWC\Jobs\ImportGuestJob;

class DwcGuestsController extends AccountBaseController
{
    use ImportExcel;
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.guests';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */

    public function importGuest()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.Guest');

        $this->view = 'dwc::guests.ajax.import';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('dwc::guests.create', $this->data);
    }
    public function importStore(ImportRequest $request)
    {
        $this->importFileProcess($request, GuestImport::class);
        $view = view('dwc::guests.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }
    public function importProcess(ImportProcessRequest $request)
    {
        $batch = $this->importJobProcess($request, GuestImport::class, ImportGuestJob::class);

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }
    public function index(GuestsDataTable $dataTable)
    {
        $this->guesttypes = DwcGuestType::all();
        $this->countries = countries();
        $this->races = DwcRaces::all();
        if (! request()->ajax()) {
            $this->guests = DwcGuest::all();
            // $this->skills = Skill::all();
        }

        // dd($this->data);

        return $dataTable->render('dwc::guests.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('app.addGuests');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        $this->salutations = Salutation::cases();
        // $this->leads = Lead::allLeads();
        $this->guests = DwcGuest::all();
        $this->horses = DwcHorse::all();
        $this->guesttypes = DwcGuestType::all();
        $this->selectedHotels = DwcHotel::all();
        $this->billingcodes = DwcBillingCode::all();
        $this->view = 'dwc::guests.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('dwc::guests.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'horse' => 'nullable|integer|exists:dwc_horses,id',
            'passport_number' => 'nullable|string|max:50|unique:dwc_guests,passport_number',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'country_phonecode' => 'required|string|max:5',
            'country' => 'required|exists:countries,id',
            'guest_type' => 'required|integer|exists:dwc_guest_types,id',
            'first_name' => 'required|string|max:100',
        ]);


        // $horse = DwcHorse::find($request->horse);
        // $race_id = $horse->races->pluck('id')->first();
        $guest = new DwcGuest();
        $guest->company_id = company()->id;
        $guest->horse_id = $request->horse;
        // $guest->race_id = $race_id;
        $guest->amendment_date = companyToYmd($request->amendment_date);
        $guest->guest_type = $request->guest_type;
        $guest->last_name = $request->last_name;
        $guest->first_name = $request->first_name;
        // $guest->title = $request->title;
        $guest->salutation = $request->salutation;
        $guest->company = $request->company;
        $guest->address_1 = $request->address;
        // $guest->address_2 = $request->address_2;
        $guest->state = $request->state;
        // $guest->zip = $request->zip;
        $guest->country = $request->country;
        // $guest->tel = $request->tel;
        // $guest->fax = $request->fax;
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
                $flight->flight_from = isset($request->flight_from[$index]) ? $request->flight_from[$index] : null;
                $flight->flight_to = isset($request->flight_to[$index]) ? $request->flight_to[$index] : null;
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
                if ($hotel_id === 'other' && !empty($request->others[$index])) {
                    $hotel->others = $request->others[$index];
                    $hotel->hotel_id = 0; // Store the entered hotel name
                } else {
                    $hotel->hotel_id = $hotel_id; // Store selected hotel ID
                    $hotel->others = null;
                }
                $hotel->room_type = $request->room_type[$index] ?? null;
                $hotel->checkin_date = $request->checkin_date[$index] ?? null;
                $hotel->sharing_with = $request->sharing_with[$index] ?? null;
                $hotel->billing_code = $request->billing_code[$index] ?? null;
                $hotel->no_of_nights = $request->no_of_nights[$index];
                $hotel->confirmation_no = $request->confirmation_no[$index];
                $hotel->checkout_date = $request->checkout_date[$index];
                $hotel->category = $request->category[$index] ?? null;
                $hotel->sub_category = $request->sub_category[$index] ?? null;
                $hotel->note_2 = $request->note_2[$index] ?? null;
                $hotel->save();
                if ($hotel) {
                    $guest->hotelReservations()->attach($hotel->id);
                }
            }
        }

        return Reply::successWithData(__('messages.guestAddedSuccessfully'), ['redirectUrl' => route('guests.index')]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->guests = DwcGuest::with('flightTickets', 'hotelReservations')->findOrFail($id);

        $tab = request('tab');

        switch ($tab) {
            case 'tickets':
                $this->view = 'dwc::guests.ajax.ticket';
                break;
            case 'hotelreservetion':
                $this->view = 'dwc::guests.ajax.hotelreservetion';
                break;
            default:
                $this->view = 'dwc::guests.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('dwc::guests.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.editGuests');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        $this->salutations = Salutation::cases();
        // $this->leads = Lead::allLeads();
        $this->guests = DwcGuest::all();
        $this->horses = DwcHorse::all();
        $this->guesttypes = DwcGuestType::all();
        $this->billingcodes = DwcBillingCode::all();
        $this->selectedHotels = DwcHotel::all();
        $this->dwcguest = DwcGuest::with('flightTickets', 'hotelReservations.hotel')->findOrFail($id);

        $this->view = 'dwc::guests.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }
        // return view('dwc::edit');
        return view('dwc::guests.ajax.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'horse' => 'nullable|integer|exists:dwc_horses,id',
            'email' => 'required|email|max:255|unique:dwc_guests,email,' . $id,
            'passport_number' => 'nullable|string|max:50|unique:dwc_guests,passport_number,' . $id,
            'mobile' => 'required|string|max:20',
            'country_phonecode' => 'required|string|max:5',
            'country' => 'required|exists:countries,id',
            'guest_type' => 'required|integer|exists:dwc_guest_types,id',
            'first_name' => 'required|string|max:100',
        ]);
        // If you expect a single race_id, use first() instead of toArray()
        // $horse = DwcHorse::find($request->horse);
        // // Extract race IDs from the many-to-many relationship
        // $race_id = $horse->races->pluck('id')->first();
        $guest = DwcGuest::findOrFail($id);
        $guest->company_id = company()->id;
        $guest->horse_id = $request->horse;
        // $guest->race_id = $race_id;
        $guest->amendment_date = companyToYmd($request->amendment_date);
        $guest->guest_type = $request->guest_type;
        $guest->last_name = $request->last_name;
        $guest->first_name = $request->first_name;
        // $guest->title = $request->title;
        $guest->salutation = $request->salutation;
        $guest->company = $request->company;
        $guest->address_1 = $request->address;
        // $guest->address_2 = $request->address_2;
        $guest->state = $request->state;
        // $guest->zip = $request->zip;
        $guest->country = $request->country;
        // $guest->tel = $request->tel;
        // $guest->fax = $request->fax;
        $guest->mobile_county_code = $request->country_phonecode;
        $guest->mobile = $request->mobile;
        $guest->email = $request->email;
        $guest->nationality = $request->nationality;
        $guest->visa_required = $request->visa_required;
        $guest->passport_number = $request->passport_number;
        $guest->save();
        // Remove old reservations & re-add
        // $guest->hotelReservations()->delete();
        // Update hotel reservations without duplicating
        if (!empty($request->hotel_id)) {
            $hotelIds = [];
            foreach ($request->hotel_id as $index => $hotel_id) {
                if ($hotel_id) {
                    $hotel = DwcHotelReservation::where('hotel_id', $hotel_id)->first();

                    if (!$hotel) {
                        $hotel = new DwcHotelReservation();
                    }

                    if ($hotel_id === 'other' && !empty($request->others[$index])) {
                        $hotel->hotel_id = 0;
                        $hotel->others = $request->others[$index];
                    } else {
                        $hotel->hotel_id = $hotel_id;
                        $hotel->others = null;
                    }

                    $hotel->room_type = $request->room_type[$index] ?? null;
                    $hotel->checkin_date = $request->checkin_date[$index] ?? null;
                    $hotel->sharing_with = $request->sharing_with[$index] ?? null;
                    $hotel->billing_code = $request->billing_code[$index] ?? null;
                    $hotel->no_of_nights = $request->no_of_nights[$index] ?? 1;
                    $hotel->confirmation_no = $request->confirmation_no[$index] ?? null;
                    $hotel->checkout_date = $request->checkout_date[$index] ?? null;
                    $hotel->category = $request->category[$index] ?? null;
                    $hotel->sub_category = $request->sub_category[$index] ?? null;
                    $hotel->note_2 = $request->note_2[$index] ?? null;
                    $hotel->save();

                    $hotelIds[] = $hotel->id;
                }
            }
            // Sync without detaching previous ones
            $guest->hotelReservations()->sync($hotelIds);
        }

        // Update flight tickets without duplicating
        if (!empty($request->flight_no) && is_array($request->flight_no)) {
            $flightIds = [];
            foreach ($request->flight_no as $index => $flightNo) {
                if (empty($flightNo)) {
                    continue;
                }

                $flight = DwcFlightTicket::where('flight_no', $flightNo)->first();
                if (!$flight) {
                    $flight = new DwcFlightTicket();
                }

                $flight->flight_no = $flightNo;
                $flight->departure_date = $request->departure_date[$index] ?? null;
                $flight->departure_time = $request->departure_time[$index] ?? null;
                $flight->arrival_date = $request->arrival_date[$index] ?? null;
                $flight->arrival_time = $request->arrival_time[$index] ?? null;
                $flight->flight_from = $request->flight_from[$index] ?? null;
                $flight->flight_to = $request->flight_to[$index] ?? null;
                $flight->flight_class = $request->flight_class[$index] ?? null;
                $flight->locator = $request->locator[$index] ?? null;
                $flight->ticket_number = $request->ticket_number[$index] ?? null;
                $flight->note_1 = $request->note_1[$index] ?? null;
                $flight->save();

                $flightIds[] = $flight->id;
            }
            // Sync flight tickets without detaching previous ones
            $guest->flightTickets()->sync($flightIds);
        }



        return Reply::successWithData(__('messages.GuestUpdatedSuccessfully'), ['redirectUrl' => route('guests.index')]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $guest = DwcGuest::findOrFail($id);

        // Delete related hotel reservations and detach them
        $guest->hotelReservations()->delete();

        // Delete related flight tickets and detach them
        $guest->flightTickets()->delete();

        // Finally, delete the guest
        $guest->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function getAirports()
    {
        $query = request('search');
        $airports = DwcAirport::select('id', DB::raw("CONCAT(`key`, ' - ', name) as text"))
            ->where('code', 'LIKE', '%' . $query . '%')
            // ->orWhere('name', 'LIKE', '%' . $query . '%')
            ->orWhere('iata', $query)
            ->limit(20)
            ->get();

        return response()->json($airports);
    }

    public function getHotels()
    {
        $query = request('search');
        $hotels = DwcHotel::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name as text']);

        return response()->json($hotels);
    }
    public function getBillingCodes()
    {
        $billingCodes = DwcBillingCode::all(['id', 'name']);
        return response()->json($billingCodes);
    }


    public function generateSelectBox()
    {
        $name = request('name');
        $flight = request('flight');
        $route = request('route');
        $label = request('label');
        $html = view('dwc::guests.ajax.create-select-ajax', compact('name', 'flight', 'route', 'label'))->render();
        return $html;
    }

    public function arraivalList(ArraivalGuestDataTable $dataTable)
    {
        return $dataTable->render('dwc::guests.arraival_list', $this->data);
    }

    public function departureList()
    {
        return view('dwc::guests.departure_list');
    }

    // public function show($guestId)
    // {
    //     $guest = DwcGuest::findOrFail($guestId);
    //     $airTickets = DB::table('dwc_guest_flight_tickets')
    //         ->where('guest_id', $guest->id)
    //         ->get()
    //         ->pluck('flight_ticket_id');
    //     $hotelBookings = DB::table('dwc_guest_hotel_reservations')
    //         ->where('guest_id', $guest->id)
    //         ->get()
    //         ->pluck('hotel_reservation_id');

    //     $this->pageTitle = $guest->name_salutation;

    //     $tab = request('tab');

    //     switch ($tab) {
    //     case 'flight_tickets':
    //         return $this->flightTickets($airTickets, $guest);
    //     case 'hotel_bookings':
    //         return $this->hotelBookings($hotelBookings, $guest);
    //     default:
    //         $this->view = 'dwc::guests.ajax.details';
    //         break;
    //     }

    //     if (request()->ajax()) {
    //         return $this->returnAjax($this->view);
    //     }

    //     $this->activeTab = $tab ?: 'details';

    //     return view('dwc::guests.show', $guest);
    // }

    public function flightTickets($airTickets, $guest)
    {
        $dataTable = new FlightsGuestDataTable($airTickets);

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';

        $this->view = 'dwc::guests.ajax.flights';

        return $dataTable->render('dwc::guests.show', $guest);
    }

    public function HotelBookings($hotelBookings, $guest)
    {
        $dataTable = new HotelGuestDataTable($hotelBookings);

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';

        $this->view = 'dwc::guests.ajax.hotels';

        return $dataTable->render('dwc::guests.show', $guest);
    }
}
