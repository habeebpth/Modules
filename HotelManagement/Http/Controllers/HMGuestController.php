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
use Modules\HotelManagement\Entities\HMGuests;
use Modules\HotelManagement\DataTables\HmGuestsDataTable;

class HMGuestController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.HMguests';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));
            abort_403(!in_array('hotelmanagement', $this->user->modules));
            return $next($request);
        });
    }
    public function index(HmGuestsDataTable $dataTable)
    {
        $this->hmguests = HMGuests::get();
        return $dataTable->render('hotelmanagement::hm-guests.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.addGuest');
        $this->company_id = user()->company_id;
        $this->countries = countries();
        // $this->leads = Lead::allLeads();
        $this->view = 'hotelmanagement::hm-guests.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-guests.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hm_guests,email',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,others',
            'address' => 'required|string',
            'nationality_id' => 'required|integer|',
            'country_phonecode' => 'required|integer|',
            'id_type' => 'required|string|max:255',
            'id_number' => 'required|string|max:255|unique:hm_guests,id_number',
            'id_photo' => 'nullable|image|mimes:jpg,jpeg,png,svg,bmp|max:2048',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        // Convert date if provided
        $dob = $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null;

        // Initialize guest model
        $guest = new HMGuests();
        $guest->first_name = $request->first_name;
        $guest->last_name = $request->last_name;
        $guest->email = $request->email;
        $guest->phone = $request->phone;
        $guest->dob = $dob;
        $guest->gender = $request->gender;
        $guest->address = $request->address;
        $guest->nationality_id = $request->nationality_id;
        $guest->country_phonecode = $request->country_phonecode;
        $guest->id_type = $request->id_type;
        $guest->id_number = $request->id_number;
        $guest->company_id = $request->company_id;

        // Handle ID photo upload if provided
        if ($request->hasFile('id_photo')) {
            $fileData = $request->file('id_photo');

            if ($fileData->isValid()) {
                $filename = Files::uploadLocalOrS3($fileData, HMGuests::FILE_PATH);
                // dd($filename);
                $guest->id_photo = $fileData->getClientOriginalName();
                $guest->hashname = $filename;
            } else {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
        }

        $guest->save();

        return Reply::successWithData(__('messages.GuestAddedSuccessfully'), [
            'redirectUrl' => route('hm-guests.index')
        ]);
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hotelmanagement::show');
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
        $this->pageTitle = __('app.editGuest');

        $this->guest = HMGuests::findOrFail($id);
        $this->countries = countries();

        // $this->leadId = $this->schedule->contact_person_id;

        $this->view = 'hotelmanagement::hm-guests.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('hotelmanagement::hm-guests.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the guest
        $guest = HMGuests::findOrFail($id);

        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hm_guests,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,others',
            'address' => 'nullable|string',
            'nationality_id' => 'required|integer|',
            'country_phonecode' => 'required|integer|',
            'id_type' => 'nullable|string|max:255',
            'id_number' => 'required|string|max:255|unique:hm_guests,id_number,' . $id,
            'id_photo' => 'nullable|image|mimes:jpg,jpeg,png,svg,bmp|max:2048',
        ]);
        $dob = $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null;
        // Update guest details
        $guest->first_name = $request->first_name;
        $guest->last_name = $request->last_name;
        $guest->email = $request->email;
        $guest->phone = $request->phone;
        $guest->dob = $dob;
        $guest->gender = $request->gender;
        $guest->address = $request->address;
        $guest->nationality_id = $request->nationality_id;
        $guest->country_phonecode = $request->country_phonecode;
        $guest->id_type = $request->id_type;
        $guest->id_number = $request->id_number;

        // Handle ID photo upload if provided
        if ($request->hasFile('id_photo')) {
            $fileData = $request->file('id_photo');
            if (!$fileData->isValid()) {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
            $filename = Files::uploadLocalOrS3($fileData, HMGuests::FILE_PATH);
            $guest->id_photo = $fileData->getClientOriginalName();
            $guest->hashname = $filename;
        }

        $guest->save();

        return Reply::successWithData(__('messages.GuestUpdatedSuccessfully'), ['redirectUrl' => route('hm-guests.index')]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $hmguests = HMGuests::findOrFail($id);
        $hmguests->delete();

        return Reply::successWithData(__('messages.GuestDeletedSuccessfully'), ['redirectUrl' => route('hm-guests.index')]);
    }
}
