<?php

namespace Modules\Synktime\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Illuminate\Http\Response;
use Modules\Synktime\Entities\Configuration;

class SynktimeSettingController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'synktime_settings';

        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index()
    {
        $this->data['pageTitle'] = 'synktime::app.menu.SynktimeSettings';
        $this->data['configurations'] = Configuration::first() ?? new Configuration(); // Ensure data is always available

        $tab = request('tab');
        $this->view = match ($tab) {
            default => 'synktime::synktime-settings.ajax.configuration',
        };

        $this->activeTab = $tab ?: 'configuration';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly([
                'status' => 'success',
                'html' => $html,
                'title' => $this->data['pageTitle'],
                'activeTab' => $this->activeTab
            ]);
        }

        return view('synktime::synktime-settings.index', $this->data);
    }

    public function updateConfiguration(Request $request, $id = null)
    {
        // Validate the request data
        $request->validate([
            'url' => 'required|url',
            'api_key' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'attendance_type' => 'required|string|in:transaction,summary',
            'day_change_time' => 'nullable|string',
            'default_start_time' => 'nullable|string',
            'default_end_time' => 'nullable|string',
            'default_working_time' => 'nullable|integer',
            'proper_checkin_checkout' => 'nullable|boolean',
            'salary_at_month_end' => 'nullable|boolean',
            'salary_date' => 'nullable|date'
        ]);

        // Check if an existing configuration exists
        $Configuration = Configuration::find($id);

        if (!$Configuration) {
            $Configuration = new Configuration();
        }

        $Configuration->url = $request->url;
        $Configuration->api_key = $request->api_key;
        $Configuration->username = $request->username;

        if ($request->filled('password')) {
            $Configuration->password = $request->password;
        }

        $Configuration->attendance_type = $request->attendance_type;
        $Configuration->day_change_time = \Carbon\Carbon::parse($request->day_change_time)->format('H:i:s');
        $Configuration->default_start_time = \Carbon\Carbon::parse($request->default_start_time)->format('H:i:s');
        $Configuration->default_end_time = \Carbon\Carbon::parse($request->default_end_time)->format('H:i:s');
        $Configuration->default_working_time = $request->default_working_time;
        $Configuration->proper_checkin_checkout = $request->boolean('proper_checkin_checkout');
        $Configuration->salary_at_month_end = $request->boolean('salary_at_month_end');
        $Configuration->salary_date = $request->salary_at_month_end ? null : companyToYmd($request->salary_date);

        $Configuration->save();

        return Reply::success(__('messages.updateSuccess'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('synktime::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('synktime::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('synktime::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id): RedirectResponse
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
