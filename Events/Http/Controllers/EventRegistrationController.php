<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Modules\Events\DataTables\EventParticipantReportDataTable;
use Modules\Events\Entities\EventCheckinPoint;
use Modules\Events\Entities\EventParticipant;
use Modules\Events\Entities\EvntEvent;
use Modules\Events\Entities\EventRegistration;
use Modules\Events\DataTables\EventRegistrationDataTable;
use Modules\Events\DataTables\EventRegistrationTwoDataTable;
use Modules\Events\DataTables\CheckedInParticipantsReportDataTable; // Import the new DataTable
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends AccountBaseController
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
    public function index(EventRegistrationDataTable $dataTable)
    {
        $this->eventregistration = EventRegistration::get();
        return $dataTable->render('events::event-registration.index', $this->data);
    }
    // public function indexTwo(EventRegistrationTwoDataTable $dataTable)
    // {
    //     $this->eventregistration = EventRegistration::get();
    //     return $dataTable->render('events::event-registration-two.index', $this->data);
    // }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $this->pageTitle = __('app.menu.AddEventRegistration');
        $this->EvntEvent = EvntEvent::get();
        $this->view = 'events::event-registration.ajax.create';

        $this->countries = countries();

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::event-registration.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'student_id' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'no_of_participants' => 'required|integer|min:1',
            'country_phonecode' => 'required|integer|',
        ]);

        $event = EvntEvent::findOrFail($request->event_id);
        $totalParticipant = EventRegistration::where('event_id', $request->event_id)->sum('no_of_participants');

        // Calculate remaining slots
        $remainingSlots = $event->no_of_seats_for_participants - $totalParticipant;
        if ($request->no_of_participants > $remainingSlots) {
            return Reply::error(__('messages.participantLimitExceeded'));
        }
        if ($request->no_of_participants > $event->maximum_participants_per_user) {
            return Reply::error(__('messages.StudentparticipantLimitExceeded'));
        }
        $SameStudent = EventRegistration::where('event_id', $request->event_id)
            ->where('student_id', $request->student_id)
            ->exists();

        if ($SameStudent) {
            return Reply::error(__('messages.id_student_already_exists'));
        }

        // Generate registration code
        $registrationCode = mt_rand(10000000, 99999999);

        // Get the last allotted seat end value
        $maxseatend = EventRegistration::where('event_id', $request->event_id)->max('allotted_seats_end');
        // dd($maxseatend);
        // Assign seats
        if (is_null($maxseatend)) {
            $allottedStart = $event->participants_seat_start;
        } else {
            $allottedStart = $maxseatend + 1;
        }
        $allottedEnd = $allottedStart + $request->no_of_participants - 1;

        // Save registration
        $registration = new EventRegistration();
        $registration->company_id = $event->company_id;
        $registration->event_id = $request->event_id;
        $registration->student_id = $request->student_id;
        $registration->name = $request->name;
        $registration->country_phonecode = $request->country_phonecode;
        $registration->mobile = $request->mobile;
        $registration->no_of_participants = $request->no_of_participants;
        $registration->allotted_seats_start = $allottedStart;
        $registration->allotted_seats_end = $allottedEnd;
        $registration->registration_code = $registrationCode;
        $registration->save();
        return Reply::successWithData(__('messages.registrationSuccess'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $registration->event_id // Pass the event ID
        ]);
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hotelmanagement::show');
    }
    public function edit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.menu.EditEventRegistration');
        $this->EvntEvent = EvntEvent::get();
        $this->registration = EventRegistration::findOrFail($id);

        $this->view = 'events::event-registration.ajax.edit';
        $this->countries = countries();
        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('events::event-registration.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'student_id' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'no_of_participants' => 'required|integer|min:1',
            'country_phonecode' => 'required|integer|',
        ]);

        $registration = EventRegistration::findOrFail($id);
        $event = EvntEvent::findOrFail($request->event_id);

        $totalParticipant = EventRegistration::where('event_id', $request->event_id)
            ->where('id', '!=', $id) // Exclude the given ID
            ->sum('no_of_participants');

        // Calculate remaining slots
        $remainingSlots = $event->no_of_seats_for_participants - $totalParticipant;
        if ($request->no_of_participants > $remainingSlots) {
            return Reply::error(__('messages.participantLimitExceeded'));
        }
        $SameStudent = EventRegistration::where('event_id', $request->event_id)
            ->where('student_id', $request->student_id)
            ->where('id', '!=', $id) // Exclude the current record
            ->exists();

        if ($SameStudent) {
            return Reply::error(__('messages.id_student_already_exists'));
        }
        $registration->event_id = $request->event_id;
        $registration->student_id = $request->student_id;
        $registration->name = $request->name;
        $registration->country_phonecode = $request->country_phonecode;
        $registration->mobile = $request->mobile;
        $registration->no_of_participants = $request->no_of_participants;
        $registration->save();
        return Reply::successWithData(__('messages.registrationUpdated'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $registration->event_id // Pass the event ID
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $EventRegistration = EventRegistration::findOrFail($id);
        $EventRegistration->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), [
            'redirectUrl' => route('event-registration.index'),
            'event_id' => $EventRegistration->event_id // Pass the event ID
        ]);
    }

    // public function participationReport(EventParticipantReportDataTable $dataTable, Request $request)
    // {
    //     $this->pageTitle = 'app.menu.EventParticipantReport';
    //     if (!request()->ajax()) {
    //         $this->events = EvntEvent::all();
    //     }
    //     $startDate = null;
    //     $endDate = null;
    //     $EventId = $request->EventId;
    //     if (!empty($request->startDate) && $request->startDate !== 'null') {
    //         $startDate = companyToDateString($request->startDate);
    //     }

    //     if (!empty($request->endDate) && $request->endDate !== 'null') {
    //         $endDate = companyToDateString($request->endDate);
    //     }

    //     // Initialize query builder and join evnt_events
    //     $query = EventParticipant::query()
    //         ->selectRaw('
    //             evnt_events.name as event_name,
    //             COUNT(event_participants.id) as total_participants,
    //             COUNT(CASE WHEN event_registrations.sex = "male" THEN 1 END) as total_male,
    //             COUNT(CASE WHEN event_registrations.sex = "female" THEN 1 END) as total_female,
    //             SUM(CASE WHEN event_registrations.kids_under_12 > 0 THEN 1 END) as total_kids
    //         ')
    //         ->join('evnt_events', 'evnt_events.id', '=', 'event_participants.event_id')
    //         ->leftJoin('event_registrations', 'event_registrations.id', '=', 'event_participants.event_registration_id')
    //         ->groupBy('event_participants.event_id');

    //     // Apply event ID filter if applicable
    //     if ($EventId != 0 && $EventId != null && $EventId != 'all') {
    //         $query->where('event_participants.event_id', '=', $EventId);
    //     }
    //     $query->where('event_participants.checkin_point_id', EventCheckinPoint::where('name', 'entrance')->first()?->id ?? 1);

    //     // Apply date range filter if both start and end dates are provided
    //     if ($startDate && $endDate) {
    //         $query->whereBetween('event_participants.created_at', [$startDate, $endDate]);
    //     }

    //     $data = $query->first();


    //     $this->total_participants = $data->total_participants;
    //     $this->total_male = $data->total_male;
    //     $this->total_female = $data->total_female;
    //     $this->total_kids = $data->total_kids;

    //     return view('events::event-registration.participation-report', $this->data);

    //     // return $dataTable->render('events::event-registration.participation-report', $this->data);
    // }

    // public function participationReport(EventParticipantReportDataTable $dataTable, Request $request)
    // {
    //     $this->pageTitle = 'app.menu.EventParticipantReport';
    //     if (!request()->ajax()) {
    //         $this->events = EvntEvent::all();
    //     }
    //     $startDate = null;
    //     $endDate = null;
    //     $EventId = $request->EventId;
    //     if (!empty($request->startDate) && $request->startDate !== 'null') {
    //         $startDate = companyToDateString($request->startDate);
    //     }

    //     if (!empty($request->endDate) && $request->endDate !== 'null') {
    //         $endDate = companyToDateString($request->endDate);
    //     }

    //     // Initialize query builder and join evnt_events
    //     $query = EventParticipant::query()
    //         ->selectRaw('
    //             evnt_events.name as event_name,
    //             COUNT(event_participants.id) as total_participants,
    //             COUNT(CASE WHEN event_registrations.sex = "male" THEN 1 END) as total_male,
    //             COUNT(CASE WHEN event_registrations.sex = "female" THEN 1 END) as total_female,
    //             SUM(CASE WHEN event_registrations.kids_under_12 > 0 THEN 1 END) as total_kids
    //         ')
    //         ->join('evnt_events', 'evnt_events.id', '=', 'event_participants.event_id')
    //         ->leftJoin('event_registrations', 'event_registrations.id', '=', 'event_participants.event_registration_id')
    //         ->groupBy('event_participants.event_id');

    //     // Apply event ID filter if applicable
    //     if ($EventId != 0 && $EventId != null && $EventId != 'all') {
    //         $query->where('event_participants.event_id', '=', $EventId);
    //     }
    //     $query->where('event_participants.checkin_point_id', EventCheckinPoint::where('name', 'entrance')->first()?->id ?? 1);

    //     // Apply date range filter if both start and end dates are provided
    //     if ($startDate && $endDate) {
    //         $query->whereBetween('event_participants.created_at', [$startDate, $endDate]);
    //     }

    //     $data = $query->first();


    //     $this->total_participants = $data->total_participants;
    //     $this->total_male = $data->total_male;
    //     $this->total_female = $data->total_female;
    //     $this->total_kids = $data->total_kids;

    //     return view('events::event-registration.participation-report', $this->data);

    //     // return $dataTable->render('events::event-registration.participation-report', $this->data);
    // }

    public function participationReport(Request $request)
    {
        $this->pageTitle = 'app.menu.EventParticipantReport';
        if (!request()->ajax()) {
            $this->events = EvntEvent::all();
        }

        $startDate = null;
        $endDate = null;
        $EventId = $request->EventId;

        if (!empty($request->startDate) && $request->startDate !== 'null') {
            $startDate = companyToDateString($request->startDate);
        } else {
            // Default to today if no start date provided
            $startDate = Carbon::today()->startOfDay()->toDateTimeString();
        }

        if (!empty($request->endDate) && $request->endDate !== 'null') {
            $endDate = companyToDateString($request->endDate);
        } else {
            // Default to today if no end date provided
            $endDate = Carbon::today()->endOfDay()->toDateTimeString();
        }

        // Get entrance checkin point ID
        $entranceCheckinId = EventCheckinPoint::where('name', 'Entrance')->first()?->id ?? 1;
        $exitCheckinId = EventCheckinPoint::where('name', 'Exit')->first()?->id ?? 2;

        // 1. TOTAL REGISTRATIONS SUMMARY
        $registrationQuery = EventRegistration::query()
            ->selectRaw('
            COUNT(id) as total_registrations,
            COUNT(CASE WHEN sex = "male" THEN 1 END) as total_male,
            COUNT(CASE WHEN sex = "female" THEN 1 END) as total_female,
            SUM(CASE WHEN kids_under_12 > 0 THEN 1 END) as total_kids
        ');

        // Apply event ID filter if applicable
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $registrationQuery->where('event_id', '=', $EventId);
        }

        // Apply date range filter if both start and end dates are provided
        if ($startDate && $endDate) {
            $registrationQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $registrationData = $registrationQuery->first();

        // 2. TOTAL PARTICIPANTS SUMMARY (checked in at entrance)
        $participantQuery = EventParticipant::query()
            ->selectRaw('
            COUNT(event_participants.id) as total_participants,
            COUNT(DISTINCT event_participants.event_registration_id) as unique_participants,
            COUNT(CASE WHEN event_registrations.sex = "male" THEN 1 END) as total_male,
            COUNT(CASE WHEN event_registrations.sex = "female" THEN 1 END) as total_female,
            SUM(CASE WHEN event_registrations.kids_under_12 > 0 THEN 1 END) as total_kids
        ')
            ->join('evnt_events', 'evnt_events.id', '=', 'event_participants.event_id')
            ->leftJoin('event_registrations', 'event_registrations.id', '=', 'event_participants.event_registration_id');

        // Apply event ID filter if applicable
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $participantQuery->where('event_participants.event_id', '=', $EventId);
        }

        // Only count entrance checkins
        $participantQuery->where('event_participants.checkin_point_id', $entranceCheckinId);

        // Apply date range filter if both start and end dates are provided
        if ($startDate && $endDate) {
            $participantQuery->whereBetween('event_participants.created_at', [$startDate, $endDate]);
        }

        $participantData = $participantQuery->first();

        // 3. CURRENTLY INSIDE SUMMARY
        $insideQuery = DB::table('event_participants as entrance')
            ->selectRaw('
            COUNT(entrance.id) as total_inside,
            COUNT(CASE WHEN er.sex = "male" THEN 1 END) as total_male_inside,
            COUNT(CASE WHEN er.sex = "female" THEN 1 END) as total_female_inside,
            SUM(CASE WHEN er.kids_under_12 > 0 THEN 1 END) as total_kids_inside
        ')
            ->leftJoin('event_registrations as er', 'er.id', '=', 'entrance.event_registration_id')
            ->leftJoin(DB::raw('(
            SELECT event_registration_id, MAX(created_at) as exit_time
            FROM event_participants
            WHERE checkin_point_id = ' . $exitCheckinId . '
            GROUP BY event_registration_id
        ) as exit_records'), function ($join) {
                $join->on('entrance.event_registration_id', '=', 'exit_records.event_registration_id');
            })
            ->where('entrance.checkin_point_id', $entranceCheckinId);

        // Query for average spend time
        $averageSpendTimeQuery = EventParticipant::query()
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, event_participants.checkin_time, NOW())) as average_spend_time')
            ->whereNotNull('event_participants.checkin_time')
            ->where('event_participants.checkin_point_id', EventCheckinPoint::where('name', 'entrance')->first()?->id ?? 1)
            ->where('event_participants.checkin_point_id', '!=', EventCheckinPoint::where('name', 'exit')->first()?->id ?? 2);


        // Apply event ID filter if applicable to all queries
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $insideQuery->where('entrance.event_id', '=', $EventId);
            $averageSpendTimeQuery->where('event_participants.event_id', '=', $EventId);
        }

        // Apply date range filter if both start and end dates are provided to all queries
        if ($startDate && $endDate) {
            $insideQuery->whereBetween('entrance.created_at', [$startDate, $endDate]);
        }

        // Either no exit record exists, or the entrance time is after the latest exit time
        $insideQuery->whereRaw('(exit_records.exit_time IS NULL OR entrance.created_at > exit_records.exit_time)');

        $insideData = $insideQuery->first();

        // 4. AVERAGE TIME SPENT
        $avgTimeQuery = DB::table('event_participants as entrance')
            ->selectRaw('
            AVG(TIMESTAMPDIFF(MINUTE, entrance.checkin_time, exit_logs.checkin_time)) as avg_minutes_spent
        ')
            ->join(DB::raw('
            event_participants as exit_logs
        '), function ($join) use ($exitCheckinId) {
                $join->on('entrance.event_registration_id', '=', 'exit_logs.event_registration_id')
                    ->where('exit_logs.checkin_point_id', '=', $exitCheckinId);
            })
            ->where('entrance.checkin_point_id', $entranceCheckinId)
            ->whereRaw('exit_logs.checkin_time > entrance.checkin_time');

        // Apply event ID filter if applicable
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $avgTimeQuery->where('entrance.event_id', '=', $EventId);
        }

        // Apply date range filter
        if ($startDate && $endDate) {
            $avgTimeQuery->whereBetween('entrance.checkin_time', [$startDate, $endDate]);
        }

        $avgTimeData = $avgTimeQuery->first();

        // Pass data to view
        $this->total_registrations = $registrationData->total_registrations ?? 0;
        $this->total_reg_male = $registrationData->total_male ?? 0;
        $this->total_reg_female = $registrationData->total_female ?? 0;
        $this->total_reg_kids = $registrationData->total_kids ?? 0;

        $this->total_participants = $participantData->total_participants ?? 0;
        $this->unique_participants = $participantData->unique_participants ?? 0;
        $this->total_male = $participantData->total_male ?? 0;
        $this->total_female = $participantData->total_female ?? 0;
        $this->total_kids = $participantData->total_kids ?? 0;

        $this->total_inside = $insideData->total_inside ?? 0;
        $this->total_male_inside = $insideData->total_male_inside ?? 0;
        $this->total_female_inside = $insideData->total_female_inside ?? 0;
        $this->total_kids_inside = $insideData->total_kids_inside ?? 0;

        $avgMinutes = round($avgTimeData->avg_minutes_spent ?? 0);
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;
        $this->avg_time_spent = ($hours > 0 ? $hours . ' hr ' : '') . $minutes . ' min';

        return view('events::event-registration.participation-report', $this->data);
    }

    public function checkedInReport(CheckedInParticipantsReportDataTable $dataTable)
    {
        $this->pageTitle = 'app.menu.CheckedInParticipantsReport';
        if (!request()->ajax()) {
            $this->events = EvntEvent::all();
        }

        return $dataTable->render('events::event-registration.checked-in-report', $this->data);
    }
}
