<?php

namespace Modules\Travels\Http\Controllers;

use App\Helper\Reply;
use App\Helper\Files;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Modules\Travels\Entities\Destination;

class TravelDestinationSettingController extends AccountBaseController
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
        $destination = Destination::findOrFail($request->destination_id);
        $destination->disable = $request->disable;
        $destination->save();

        return response()->json(['status' => 'success', 'message' => __('Airline status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        $this->countries = countries();
        return view('travels::travel-settings.destination.create-destination-modal', $this->data);

    }

    public function store(Request $request)
    {

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|integer',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'description' => 'nullable',
            'image.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        // If logos are uploaded, handle multiple files
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $fileData) {
                // Upload each file using the custom method Files::uploadLocalOrS3
                $filename = Files::uploadLocalOrS3($fileData, Destination::FILE_PATH);
                // Create a new destination instance
                $destination = new Destination();
                $destination->name = $request->name;
                $destination->country_id = $request->country_id;
                $destination->city = $request->city;
                $destination->state = $request->state;
                $destination->description = $request->description;
                $destination->image = $fileData->getClientOriginalName();  // Store original logo name
                $destination->hashname = $filename;  // Store hashed name (generated after upload)
                $destination->company_id = $request->company_id;
                $destination->disable = 'y';
                // Save the destination record
                $destination->save();
            }
        } else {
            // Create a new destination instance
            $destination = new Destination();
            $destination->name = $request->name;
            $destination->country_id = $request->country_id;
            $destination->city = $request->city;
            $destination->state = $request->state;
            $destination->description = $request->description;
            $destination->company_id = $request->company_id;
            $destination->disable = 'y';
            // Save the airline to the database
            $destination->save();
        }
        // Retrieve all destinations and prepare options for the response
        $destinations = Destination::all();
        $options = BaseModel::options($destinations);

        // Return a success response with the options data
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
        $this->destination = Destination::findOrFail($id);
        $this->countries = countries();
        return view('travels::travel-settings.destination.edit-destination-modal', $this->data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|integer',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'description' => 'nullable',
            'image.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
        ]);

        $destination = Destination::findOrFail($id);
        // Handle multiple logo uploads
        if ($request->hasFile('image')) {
            // Optionally, delete existing logos if they need to be replaced
            if (!empty($destination->hashname)) {
                $oldHashnames = json_decode($destination->hashname, true);
                foreach ($oldHashnames as $hashname) {
                    Storage::disk('public')->delete(Destination::FILE_PATH . '/' . $hashname);
                }
            }

            $uploadedimages = []; // To store uploaded file details
            foreach ($request->file('image') as $fileData) {
                // Upload each file using the custom method Files::uploadLocalOrS3
                $filename = Files::uploadLocalOrS3($fileData, Destination::FILE_PATH);

                // Store the original file name and hashname
                $uploadedimages[] = [
                    'original_name' => $fileData->getClientOriginalName(),
                    'hashname' => $filename,
                ];
            }

            // Save the original file names and hashnames as JSON (or handle as needed)
            $destination->logo = json_encode(array_column($uploadedimages, 'original_name'));
            $destination->hashname = json_encode(array_column($uploadedimages, 'hashname'));
        }
        $destination->name = $request->name;
        $destination->country_id = $request->country_id;
        $destination->city = $request->city;
        $destination->state = $request->state;
        $destination->description = $request->description;
        $destination->save();

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

        Destination::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
