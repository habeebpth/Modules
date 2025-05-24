<?php

namespace Modules\Travels\Http\Controllers;

use App\Helper\Reply;
use App\Helper\Files;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Modules\Travels\Entities\Airline;

class TravelAirlineSettingController extends AccountBaseController
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
        $airline = Airline::findOrFail($request->airline_id);
        $airline->disable = $request->disable;
        $airline->save();

        return response()->json(['status' => 'success', 'message' => __('Airline status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        $this->countries = countries();
        return view('travels::travel-settings.airline.create-airline-modal', $this->data);

    }
    public function store(Request $request)
    {

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:airlines,code',
            'country_id' => 'required|integer',
            'contact_number' => 'nullable|string|max:15',
            'website' => 'nullable|url|max:255',
            'company_id' => 'required|integer|exists:companies,id',
            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
        ]);

        // If logos are uploaded, handle multiple files
        if ($request->hasFile('logo')) {
            foreach ($request->file('logo') as $fileData) {
                // Upload each file using the custom method Files::uploadLocalOrS3
                $filename = Files::uploadLocalOrS3($fileData, Airline::FILE_PATH);
                // Create a new airline record for each logo
                $airline = new Airline();
                $airline->name = $request->name;
                $airline->code = $request->code;
                $airline->country_id = $request->country_id;
                $airline->contact_number = $request->contact_number;
                $airline->website = $request->website;
                $airline->logo = $fileData->getClientOriginalName();  // Store original logo name
                $airline->hashname = $filename;  // Store hashed name (generated after upload)
                $airline->company_id = $request->company_id;
                $airline->disable = 'y';

                // Save the airline to the database
                $airline->save();
            }
        } else {
            // If no logos are uploaded, create a single airline record
            $airline = new Airline();
            $airline->name = $request->name;
            $airline->code = $request->code;
            $airline->country_id = $request->country_id;
            $airline->contact_number = $request->contact_number;
            $airline->website = $request->website;
            $airline->company_id = $request->company_id;
            $airline->disable = 'y';

            // Save the airline to the database
            $airline->save();
        }

        // Retrieve all airlines and prepare options for the response
        $allAirlines = Airline::get();
        $options = BaseModel::options($allAirlines);

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
        $this->airline = Airline::findOrFail($id);
        $this->countries = countries();
        return view('travels::travel-settings.airline.edit-airline-modal', $this->data);
    }


    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                Rule::unique('airlines', 'code')->ignore($id)
            ],
            'country_id' => 'required|integer',
            'contact_number' => 'nullable|string|max:15',
            'website' => 'nullable|url|max:255',
            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
        ]);

        // Find the airline by ID or throw a 404 error if not found
        $airline = Airline::findOrFail($id);

        // Handle logo uploads if provided
        if ($request->hasFile('logo') && is_array($request->file('logo'))) {
            // Delete existing logos before replacing them
            if (!empty($airline->hashname)) {
                $oldHashnames = json_decode($airline->hashname, true);
                foreach ($oldHashnames as $hashname) {
                    Storage::disk('public')->delete(Airline::FILE_PATH . '/' . $hashname);
                }
            }

            $uploadedLogos = []; // Store uploaded file details
            foreach ($request->file('logo') as $fileData) {
                if (!$fileData->isValid()) {
                    return response()->json(['message' => 'Invalid file uploaded.'], 400);
                }

                // Upload the file using a custom method
                $filename = Files::uploadLocalOrS3($fileData, Airline::FILE_PATH);

                $uploadedLogos[] = [
                    'original_name' => $fileData->getClientOriginalName(),
                    'hashname' => $filename,
                ];
            }

            // Save the original file names and hash names as JSON
            $airline->logo = json_encode(array_column($uploadedLogos, 'original_name'));
            $airline->hashname = json_encode(array_column($uploadedLogos, 'hashname'));
        }

        // Update the airline details
        $airline->name = $request->name;
        $airline->code = $request->code;
        $airline->country_id = $request->country_id;
        $airline->contact_number = $request->contact_number;
        $airline->website = $request->website;
        $airline->disable = 'y';

        // Save the updated airline record
        $airline->save();

        // Return a success response
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

        Airline::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
