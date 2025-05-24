<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Events\Entities\EvntEvent;
use Modules\Events\Entities\EventCheckinPoint;
use Modules\Events\DataTables\EventRegistrationDataTable;
use App\Helper\Files;

class EventCheckinPointsController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.EventRegistration';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index()
{
    return redirect()->route('events.index');
}

public function create()
{
    return view('events::evnt-events.checkin-point.create', $this->data);
}

public function store(Request $request)
{
    $checkin = new EventCheckinPoint();
    $checkin->event_id = $request->event_id;
    $checkin->name = $request->name;
    $checkin->code = $request->code;
    $checkin->number = $request->number;
    $checkin->description = $request->description;
    $checkin->company_id = company()->id;
    $checkin->added_by = user()->id;

    if ($request->hasFile('image')) {
        $fileData = $request->file('image');

        if ($fileData->isValid()) {
            $filename = Files::uploadLocalOrS3($fileData, EventCheckinPoint::FILE_PATH);
            $checkin->image = $fileData->getClientOriginalName();
            $checkin->hashname = $filename;
        } else {
            return response()->json(['message' => 'Invalid file uploaded.'], 400);
        }
    }
    $checkin->save();

    return Reply::success(__('messages.recordSaved'));
}

// public function show($id)
// {
//     $this->checkin = EventCheckinPoint::findOrFail($id);
//     $this->view = 'events.ajax.checkin-view';

//     if (request()->ajax()) {
//         return $this->returnAjax($this->view);
//     }

//     return view('events.index', $this->data);
// }

public function edit($id)
{
    $this->checkinPoint = EventCheckinPoint::findOrFail($id);
    return view('events::evnt-events.checkin-point.edit', $this->data);
}

public function update(Request $request, $id)
{
    $checkin = EventCheckinPoint::findOrFail($id);

    $checkin->event_id = $request->event_id;
    $checkin->name = $request->name;
    $checkin->code = $request->code;
    $checkin->number = $request->number;
    $checkin->description = $request->description;

    if ($request->hasFile('image')) {
        $fileData = $request->file('image');

        if ($fileData->isValid()) {
            // Delete old file if exists
            if ($checkin->hashname) {
                Files::deleteFile($checkin->hashname, EventCheckinPoint::FILE_PATH);
            }

            $filename = Files::uploadLocalOrS3($fileData, EventCheckinPoint::FILE_PATH);
            $checkin->image = $fileData->getClientOriginalName();
            $checkin->hashname = $filename;
        } else {
            return response()->json(['message' => 'Invalid file uploaded.'], 400);
        }
    }

    $checkin->save();

    return Reply::success(__('messages.updateSuccess'));
}


public function destroy($id)
{
    $checkin = EventCheckinPoint::findOrFail($id);

    // Delete the stored file using hashname if available
    if ($checkin->hashname) {
        Files::deleteFile($checkin->hashname, EventCheckinPoint::FILE_PATH);
    }

    $checkin->delete();

    return Reply::success(__('messages.deleteSuccess'));
}


}
