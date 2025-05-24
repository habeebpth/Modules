<?php

namespace Modules\Events\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Carbon\Carbon;
use App\Models\EventAttendee;
use App\Events\EventInviteEvent;
use App\Events\EventInviteMentionEvent;
use App\Events\EventStatusNoteEvent;
use App\Models\User;
use Modules\Events\Entities\Panchayat;
use Modules\Events\Entities\District;
use App\Http\Requests\Events\StoreEvent;
use App\Http\Requests\Events\StoreEventNote;
use App\Http\Requests\Events\UpdateEvent;
use Modules\Events\DataTables\EvntEventDataTable;
use Modules\Events\DataTables\EventRegistrationDataTable;
use Modules\Events\DataTables\EventParticipantDataTable;
use Modules\Events\DataTables\EventRegistrationTwoDataTable;
use App\Models\MentionUser;
use Illuminate\Http\Request;
use Modules\Events\Entities\EvntEvent;
use Modules\Events\Entities\EventCheckinPoint;
use Modules\Events\Entities\EventRegistration;
use Modules\Events\Entities\EventParticipant;
use Modules\Accounting\Entities\AccountType;
use App\Helper\Files;
use Illuminate\Validation\Rule;
use Modules\HotelManagement\Entities\RoomType;

class EvntEventController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.events';
        // $this->middleware(function ($request, $next) {
        //     abort_403(!in_array('events', $this->user->modules));
        //     return $next($request);
        // });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //  public function index()
    //  {
    //          $this->clients = User::allClients();
    //          $this->employees = User::allEmployees(null, true);


    //      if (request('start') && request('end')) {
    //          $model = EvntEvent::with('attendee', 'attendee.user');


    //          if (request()->clientId && request()->clientId != 'all') {
    //              $clientId = request()->clientId;
    //              $model->whereHas('attendee.user', function ($query) use ($clientId) {
    //                  $query->where('user_id', $clientId);
    //              });
    //          }

    //          if (request()->employeeId && request()->employeeId != 'all' && request()->employeeId != 'undefined') {
    //              $employeeId = request()->employeeId;
    //              $model->whereHas('attendee.user', function ($query) use ($employeeId) {
    //                  $query->where('user_id', $employeeId);
    //              });
    //          }

    //          if (request()->searchText && request()->searchText != 'all') {
    //              $model->where('name', 'like', '%' . request('searchText') . '%');
    //          }
    //          $events = $model->get();

    //          $eventData = array();

    //          foreach ($events as $key => $event) {
    //              $eventData[] = [
    //                  'id' => $event->id,
    //                  'title' => $event->name,
    //                  'start' => $event->start_date,
    //                  'end' => $event->end_date,
    //                  'color' => $event->label_color
    //              ];
    //          }

    //          return $eventData;
    //      }

    //      return view('events::evnt-events.index', $this->data);

    //  }
    public function index(EvntEventDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_events');
        $this->EvntEvent = EvntEvent::get();
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        return $dataTable->render('events::evnt-events.indexs', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_events');
        $this->viewPermission = user()->permission('view_events');
        abort_403($this->addPermission != 'all');
        $this->employees = User::allEmployees(null, false);
        $this->clients = User::allClients();
        $this->pageTitle = __('modules.events.addEvent');
        $userData = [];

        $usersData = $this->employees;

        foreach ($usersData as $user) {

            $url = route('employees.show', [$user->id]);

            $userData[] = ['id' => $user->id, 'value' => $user->name, 'image' => $user->image_url, 'link' => $url];
        }

        $this->userData = $userData;
        //  $this->teams = Team::all();
        $this->view = 'events::evnt-events.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::evnt-events.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'registration_link_enable' => 'nullable|',
            'registration_last_date' => 'nullable',
            'registration_last_time' => 'nullable',
            'label_color' => 'nullable|string|max:50',
            'registration_fees_enable' => 'nullable',
            'registration_fees_amount' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'maximum_participants' => 'nullable|integer|min:1',
            'maximum_participants_per_user' => 'nullable|integer|min:1',
            'no_of_seats_for_guests' => 'nullable|integer|min:1',
            'guest_seat_start' => 'nullable|integer|min:1',
            'guest_seat_end' => 'nullable|integer|min:1|gte:guest_seat_start',
            'no_of_seats_for_participants' => 'nullable|integer|min:1',
            'participants_seat_start' => 'nullable|integer|min:1',
            'participants_seat_end' => 'nullable|integer|min:1|gte:participants_seat_start',
            'slug' => 'required|string|max:255|unique:evnt_events,slug',
            'status' => 'required|string|in:Upcoming,Completed,Cancelled',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,svg,bmp|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg,bmp|max:1024',
            'brocher' => 'nullable|mimes:pdf|max:5120',
        ]);
        $this->addPermission = user()->permission('add_events');
        abort_403($this->addPermission != 'all');
        $company_id = user()->company_id;
        $event = new EvntEvent();
        $event->name = $request->event_name;
        $event->description = trim_editor($request->description);

        $start_date_time = Carbon::createFromFormat($this->company->date_format, $request->start_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->start_time)->format('H:i:s');
        $event->start_date_time = Carbon::parse($start_date_time)->setTimezone('UTC');

        $end_date_time = Carbon::createFromFormat($this->company->date_format, $request->end_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->end_time)->format('H:i:s');
        $event->end_date_time = Carbon::parse($end_date_time)->setTimezone('UTC');
        $event->registration_link_enable = $request->registration_link_enable;
        $registration_last_date_time = Carbon::createFromFormat($this->company->date_format, $request->registration_last_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->registration_last_time)->format('H:i:s');
        $event->registration_last_date_time = Carbon::parse($registration_last_date_time)->setTimezone('UTC');
        $event->label_color = $request->label_color;
        $event->registration_fees_enable = $request->registration_fees_enable;
        $event->registration_fees_amount = $request->registration_fees_amount;
        $event->location = $request->location;
        $event->maximum_participants = $request->maximum_participants;
        $event->maximum_participants_per_user = $request->maximum_participants_per_user;
        $event->no_of_seats_for_guests = $request->no_of_seats_for_guests;
        $event->guest_seat_start = $request->guest_seat_start;
        $event->guest_seat_end = $request->guest_seat_end;
        $event->no_of_seats_for_participants = $request->no_of_seats_for_participants;
        $event->participants_seat_start = $request->participants_seat_start;
        $event->participants_seat_end = $request->participants_seat_end;
        $event->slug = $request->slug;
        $event->company_id = $company_id;
        $event->status = $request->status;
        if ($request->hasFile('banner')) {
            $fileData = $request->file('banner');

            if ($fileData->isValid()) {
                $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
                $event->banner =  $filename;
            } else {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
        }
        if ($request->hasFile('icon')) {
            $fileData = $request->file('icon');

            if ($fileData->isValid()) {
                $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
                $event->icon =  $filename;
            } else {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
        }
        if ($request->hasFile('brocher')) {
            $fileData = $request->file('brocher');

            if ($fileData->isValid()) {
                $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
                $event->brocher =  $filename;
            } else {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
        }
        $event->save();

        $event->touch();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('events.index'), 'eventId' => $event->id]);
    }

    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_events');

        abort_403(!(
            $this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->appreciation->added_by == user()->id)
            || ($this->editPermission == 'owned' && $this->appreciation->award_to == user()->id)
            || ($this->editPermission == 'both' && ($this->appreciation->added_by == user()->id || $this->appreciation->award_to == user()->id))
        ));
        $this->event = EvntEvent::with('attendee', 'attendee.user', 'files')->findOrFail($id);
        $attendeesIds = $this->event->attendee->pluck('user_id')->toArray();

        $this->pageTitle = __('app.menu.editEvents');

        $this->employees = User::allEmployees();
        $this->clients = User::allClients();
        //  $this->teams = Team::all();
        $userData = [];

        $this->clientIds = $this->event->attendee
            ->filter(function ($item) {
                return in_array('client', $item->user->roles->pluck('name')->toArray());
            });

        $this->userIds = $this->event->attendee
            ->filter(function ($item) {
                return in_array('employee', $item->user->roles->pluck('name')->toArray());
            });

        $usersData = $this->employees;

        foreach ($usersData as $user) {

            $url = route('employees.show', [$user->id]);

            $userData[] = ['id' => $user->id, 'value' => $user->name, 'image' => $user->image_url, 'link' => $url];
        }

        $this->userData = $userData;

        $attendeeArray = [];

        foreach ($this->event->attendee as $key => $item) {
            $attendeeArray[] = $item->user_id;
        }

        $this->attendeeArray = $attendeeArray;
        $this->departments = json_decode($this->event->departments, true);

        $this->view = 'events::evnt-events.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::evnt-events.create', $this->data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'registration_link_enable' => 'nullable|',
            'registration_last_date' => 'required_if:registration_link_enable,true',
            'registration_last_time' => 'required_if:registration_link_enable,true',
            'label_color' => 'nullable|string|max:7',
            'registration_fees_enable' => 'nullable|',
            'registration_fees_amount' => 'required_if:registration_fees_enable,true|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'maximum_participants' => 'nullable|integer|min:1',
            'maximum_participants_per_user' => 'nullable|integer|min:1',
            'no_of_seats_for_guests' => 'nullable|integer|min:1',
            'guest_seat_start' => 'nullable|integer|min:1',
            'guest_seat_end' => 'nullable|integer|min:1|gte:guest_seat_start',
            'no_of_seats_for_participants' => 'nullable|integer|min:1',
            'participants_seat_start' => 'nullable|integer|min:1',
            'participants_seat_end' => 'nullable|integer|min:1|gte:participants_seat_start',
            'slug' => 'required|string|unique:evnt_events,slug,' . $id,
            'banner' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'brocher' => 'nullable|file|mimes:pdf|max:5120',
        ]);
        $event = EvntEvent::findOrFail($id);

        $event->name = $request->event_name;
        $event->description = trim_editor($request->description);

        $start_date_time = Carbon::createFromFormat($this->company->date_format, $request->start_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->start_time)->format('H:i:s');
        $event->start_date_time = Carbon::parse($start_date_time)->setTimezone('UTC');

        $end_date_time = Carbon::createFromFormat($this->company->date_format, $request->end_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->end_time)->format('H:i:s');
        $event->end_date_time = Carbon::parse($end_date_time)->setTimezone('UTC');

        $event->registration_link_enable = $request->registration_link_enable;

        $registration_last_date_time = Carbon::createFromFormat($this->company->date_format, $request->registration_last_date, $this->company->timezone)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->company->time_format, $request->registration_last_time)->format('H:i:s');
        $event->registration_last_date_time = Carbon::parse($registration_last_date_time)->setTimezone('UTC');

        $event->label_color = $request->label_color;
        $event->registration_fees_enable = $request->registration_fees_enable;
        $event->registration_fees_amount = $request->registration_fees_amount;
        $event->location = $request->location;
        $event->maximum_participants = $request->maximum_participants;
        $event->maximum_participants_per_user = $request->maximum_participants_per_user;
        $event->no_of_seats_for_guests = $request->no_of_seats_for_guests;
        $event->guest_seat_start = $request->guest_seat_start;
        $event->guest_seat_end = $request->guest_seat_end;
        $event->no_of_seats_for_participants = $request->no_of_seats_for_participants;
        $event->participants_seat_start = $request->participants_seat_start;
        $event->participants_seat_end = $request->participants_seat_end;
        $event->slug = $request->slug;
        $event->status = $request->status;
        if ($request->hasFile('brocher')) {
            $fileData = $request->file('brocher');
            if (!$fileData->isValid()) {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
            $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
            $event->brocher = $filename;
        }
        if ($request->hasFile('icon')) {
            $fileData = $request->file('icon');
            if (!$fileData->isValid()) {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
            $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
            $event->icon = $filename;
        }
        if ($request->hasFile('banner')) {
            $fileData = $request->file('banner');
            if (!$fileData->isValid()) {
                return response()->json(['message' => 'Invalid file uploaded.'], 400);
            }
            $filename = Files::uploadLocalOrS3($fileData, EvntEvent::FILE_PATH);
            $event->banner = $filename;
        }
        $event->save();

        return Reply::successWithData(__('messages.recordUpdated'), [
            'redirectUrl' => route('events.index'),
            'eventId' => $event->id
        ]);
    }
    public function show($id)
    {
        $this->viewPermission = user()->permission('view_events');
        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->appreciation->added_by == user()->id)
            || ($this->viewPermission == 'owned' && $this->appreciation->award_to == user()->id)
            || ($this->viewPermission == 'both' && ($this->appreciation->added_by == user()->id || $this->appreciation->award_to == user()->id))
        ));
        $this->event = EvntEvent::with('attendee', 'attendee.user', 'user')->findOrFail($id);
        $this->checkinpoints = EventCheckinPoint::where('event_id',$id)->get();

        $this->pageTitle = 'app.menu.events';

        $tab = request('tab');

        switch ($tab) {
            case 'event-participant':
                return $this->EventParticipant();
            case 'event-registration':
                return $this->EventRegistration();
                case 'event-registration-two':
                    return $this->EventRegistrationTwo();
                case 'checkin-point':
                    $this->view = 'events::evnt-events.ajax.checkin-point';
                    break;
            default:
                $this->view = 'events::evnt-events.ajax.overview';
                break;
        }

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('events::evnt-events.show', $this->data);
    }
    public function EventRegistration()
    {
        $dataTable = new EventRegistrationDataTable();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'event-registration';

        $this->view = 'events::evnt-events.ajax.event-registration';

        return $dataTable->render('events::evnt-events.show', $this->data);
    }
    public function EventRegistrationTwo()
    {
        $dataTable = new EventRegistrationTwoDataTable();
        $this->districts = District::all();
        $this->panchayath = Panchayat::all();
        $tab = request('tab');
        $this->activeTab = $tab ?: 'event-registration-two';

        $this->view = 'events::evnt-events.ajax.event-registration-two';

        return $dataTable->render('events::evnt-events.show', $this->data);
    }
    public function Registrationcreate($id)
    {
        // Fetch the event details using the provided ID
        $this->event = EvntEvent::findOrFail($id);
        $this->pageTitle = __('app.menu.AddEventRegistration');
        $this->view = 'events::evnt-events.ajax.registration-create';

        $this->countries = countries();

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::evnt-events.create', $this->data);
    }
    public function Registrationedit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.menu.EditEventRegistration');
        $this->registration = EventRegistration::findOrFail($id);

        $this->view = 'events::evnt-events.ajax.registration-edit';
        $this->countries = countries();
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::event-registration.create', $this->data);
    }

    public function EventParticipant()
    {
        $dataTable = new EventParticipantDataTable();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'event-participant';

        $this->view = 'events::evnt-events.ajax.event-participant';

        return $dataTable->render('events::evnt-events.show', $this->data);
    }
    public function Participantcreate($id)
    {
        // Fetch the event details using the provided ID
        $this->event = EvntEvent::findOrFail($id);
        $this->registrations = EventRegistration::where('event_id', $id)->get();
        $this->pageTitle = __('app.menu.AddEventParticipant');
        $this->view = 'events::evnt-events.ajax.participant-create';

        $this->countries = countries();

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::evnt-events.create', $this->data);
    }

    public function Participantstore(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'event_registration_id' => 'required|integer|exists:event_registrations,id',
            'no_of_participants' => 'required|integer|min:1',
            'no_of_seats_filled_start' => 'required|integer|min:1',
            'no_of_seats_filled_end' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $event = EvntEvent::findOrFail($request->event_id);
        // Check if the participant already exists for the same event
        $SameStudentExists = EventParticipant::where('event_id', $request->event_id)
            ->where('event_registration_id', $request->event_registration_id)
            ->exists();

        if ($SameStudentExists) {
            return Reply::error(__('messages.id_student_already_exists'));
        }

        // Check if seat numbers overlap
        $SameSeatExists = EventParticipant::where('event_id', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('no_of_seats_filled_start', '<=', $request->no_of_seats_filled_end)
                        ->where('no_of_seats_filled_end', '>=', $request->no_of_seats_filled_start);
                });
            })
            ->exists();
        if ($SameSeatExists) {
            return Reply::error(__('messages.id_seat_already_taken'));
        }
        // Save registration
        $Participant = new EventParticipant();
        $Participant->company_id = $event->company_id;
        $Participant->event_id = $request->event_id;
        $Participant->event_registration_id = $request->event_registration_id;
        $Participant->no_of_seats_filled_start = $request->no_of_seats_filled_start;
        $Participant->no_of_seats_filled_end = $request->no_of_seats_filled_end;
        $Participant->no_of_participants = $request->no_of_participants;
        $Participant->remarks = $request->remarks;
        $Participant->checkin_time = now();
        $Participant->save();

        return Reply::successWithData(__('messages.ParticipantAddedSuccess'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $Participant->event_id // Pass the event ID
        ]);
    }

    public function Participantedit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.menu.EditEventParticipant');
        $this->participant = EventParticipant::findOrFail($id);
        $eventid = $this->participant->event_id;
        $this->registrations = EventRegistration::where('event_id', $eventid)->get();
        $this->view = 'events::evnt-events.ajax.participant-edit';
        $this->countries = countries();
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::event-registration.create', $this->data);
    }
    public function ParticipantUpdate(Request $request, $id)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'event_registration_id' => 'required|integer|exists:event_registrations,id',
            'no_of_participants' => 'required|integer|min:1',
            'no_of_seats_filled_start' => 'required|integer|min:1',
            'no_of_seats_filled_end' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:255',
        ]);
        $SameStudentExists = EventParticipant::where('event_id', $request->event_id)
            ->where('id', '!=', $id)
            ->where('event_registration_id', $request->event_registration_id)
            ->exists();

        if ($SameStudentExists) {
            return Reply::error(__('messages.id_student_already_exists'));
        }
        $SameSeatExists = EventParticipant::where('event_id', $request->event_id)
            ->where('no_of_seats_filled_start', '<=', $request->no_of_seats_filled_end)
            ->where('no_of_seats_filled_end', '>=', $request->no_of_seats_filled_start)
            ->where('id', '!=', $id) // Exclude the current participant
            ->exists();
        if ($SameSeatExists) {
            return Reply::error(__('messages.id_seat_already_taken'));
        }
        $Participant = EventParticipant::findOrFail($id);
        // Update Participant details
        $Participant->event_id = $request->event_id;
        $Participant->event_registration_id = $request->event_registration_id;
        $Participant->no_of_seats_filled_start = $request->no_of_seats_filled_start;
        $Participant->no_of_seats_filled_end = $request->no_of_seats_filled_end;
        $Participant->no_of_participants = $request->no_of_participants;
        $Participant->remarks = $request->remarks;
        $Participant->save();

        return Reply::successWithData(__('messages.ParticipantUpdateSuccess'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $Participant->event_id // Pass the event ID
        ]);
    }
    public function ParticipantDelete($id)
    {

        $EventParticipant = EventParticipant::findOrFail($id);
        $EventParticipant->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $EventParticipant->event_id // Pass the event ID
        ]);
    }
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_events');
        abort_403(!(
            $this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $this->appreciation->added_by == user()->id)
            || ($this->deletePermission == 'owned' && $this->appreciation->award_to == user()->id)
            || ($this->deletePermission == 'both' && ($this->appreciation->added_by == user()->id || $this->appreciation->award_to == user()->id))
        ));

        $event = EvntEvent::with('attendee', 'attendee.user')->findOrFail($id);
        EvntEvent::destroy($id);
        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('events.index')]);
    }

    public function monthlyOn(Request $request)
    {
        $date = Carbon::createFromFormat($this->company->date_format, $request->date);

        $week = __('app.eventDay.' . $date->weekOfMonth);
        $day = $date->translatedFormat('l');

        return Reply::dataOnly(['message' => __('app.eventMonthlyOn', ['week' => $week, 'day' => $day])]);
    }

    public function updateStatus(StoreEventNote $request, $id)
    {
        $event = Event::findOrFail($id);
        $attendees = $event->attendee->pluck('user');

        $event->status = $request->status;
        $event->note = $request->note;
        $event->update();

        if ($request->status == 'cancelled') {
            event(new EventStatusNoteEvent($event, $attendees));
        }

        return Reply::success(__('messages.updateSuccess'));
    }

    public function eventStatusNote(Request $request, $id)
    {
        $this->event = Event::findOrFail($id);
        $this->status = $request->status;

        return view('event-calendar.event-status-note', $this->data);
    }
}
