<?php

namespace Modules\HotelManagement\Http\Controllers;

use App\Helper\Reply;
use App\Helper\Files;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Modules\HotelManagement\Entities\HMBookingSource;

class HmBookingSettingController extends AccountBaseController
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
        $hmbookingsource = HMBookingSource::findOrFail($request->hmbookingsource_id);
        $hmbookingsource->disable = $request->disable;
        $hmbookingsource->save();

        return response()->json(['status' => 'success', 'message' => __('Booking Source status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('hotelmanagement::hotelmanagement-settings.hmbookingsource.create-hmbookingsource-modal', $this->data);

    }

    /**
     * @param StoreLeadSource $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
            'url' => 'nullable|url|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        // Initialize the response data for the booking sources
        $hmbookingsource = new HMBookingSource();
        $hmbookingsource->name = $request->name;
        $hmbookingsource->description = $request->description;
        $hmbookingsource->url = $request->url;
        $hmbookingsource->company_id = $request->company_id;
        $hmbookingsource->disable = 'y';

        // Handle logo uploads if provided
        if ($request->hasFile('logo')) {
            foreach ($request->file('logo') as $fileData) {
                // Validate the file individually
                if (!$fileData->isValid()) {
                    return response()->json(['message' => 'Invalid file uploaded.'], 400);
                }

                // Upload each file using a custom method for local/S3 storage
                $filename = Files::uploadLocalOrS3($fileData, HMBookingSource::FILE_PATH);

                // Store the uploaded file details
                $hmbookingsource->logo = $fileData->getClientOriginalName();
                $hmbookingsource->hashname = $filename;

                // Save the new record with the logo
                $hmbookingsource->save();
            }
        } else {
            // Save the record without logos (if no files are uploaded)
            $hmbookingsource->save();
        }

        // Retrieve all booking sources and prepare the options for the response
        $hmbookingsources = HMBookingSource::all();
        $options = BaseModel::options($hmbookingsources);

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
        $this->hmbookingsources = HMBookingSource::findOrFail($id);
        // dd($this->floor);

        return view('hotelmanagement::hotelmanagement-settings.hmbookingsource.edit-hmbookingsource-modal', $this->data);
    }

    /**
     * @param UpdateLeadSource $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,bmp,svg|max:2048', // Validate each logo
            'url' => 'nullable|url|max:255',
        ]);

        // Find the existing HMBookingSource record
        $hmbookingsource = HMBookingSource::findOrFail($id);

        // Update the fields with the new data
        $hmbookingsource->name = $request->name;
        $hmbookingsource->description = $request->description;
        $hmbookingsource->url = $request->url;
        $hmbookingsource->disable = 'y';

        // Handle logo uploads if provided
        if ($request->hasFile('logo')) {
            // Check if there's an existing hashname (indicating a previous logo)
            if (!empty($hmbookingsource->hashname)) {
                // Remove the old logo files from storage
                $oldHashnames = json_decode($hmbookingsource->hashname, true);
                if ($oldHashnames && is_array($oldHashnames)) {
                    foreach ($oldHashnames as $hashname) {
                        // Delete old logo files
                        Storage::disk('public')->delete(HMBookingSource::FILE_PATH . '/' . $hashname);
                    }
                }
            }

            // Process new logo files
            $newHashnames = [];
            foreach ($request->file('logo') as $fileData) {
                // Validate the file individually
                if (!$fileData->isValid()) {
                    return response()->json(['message' => 'Invalid file uploaded.'], 400);
                }

                // Upload the file using a custom method for local/S3 storage
                $filename = Files::uploadLocalOrS3($fileData, HMBookingSource::FILE_PATH);

                // Store the uploaded file details
                $newHashnames[] = $filename; // Collect new hashnames for future reference
            }

            // Update hashname field with the new logos' filenames
            $hmbookingsource->logo = implode(',', array_map(function ($file) {
                return basename($file); // Store original filenames (optional)
            }, $newHashnames));
            $hmbookingsource->hashname = json_encode($newHashnames); // Store the new hashnames
        }

        // Save the updated record
        $hmbookingsource->save();

        // Retrieve all booking sources and prepare the options for the response
        $hmbookingsources = HMBookingSource::all();
        $options = BaseModel::options($hmbookingsources);

        // Return a success response with the options data
        return Reply::successWithData(__('messages.recordUpdated'), ['data' => $options]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        HMBookingSource::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
