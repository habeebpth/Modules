<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\LeadSetting;
use App\Models\LeadCategory;
use App\Models\LeadPipeline;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\User;
use Carbon\Carbon;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Accounting\Entities\AccountCategory;
use Modules\Accounting\Entities\AccountType;
use Modules\Accounting\Entities\FinanceSetting;
use Modules\HotelManagement\Entities\Facility;
use Modules\HotelManagement\Entities\Floor;
use Modules\HotelManagement\Entities\RoomType;
use Modules\HotelManagement\Entities\Service;

class AccountingSettingController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'accounting_settings';

        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function updateFinanceSetting(Request $request, $id)
    {
        $request->validate([
            'day' => 'required|integer|between:1,31',
            'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'currency_id' => 'required|integer',
        ]);

        $financeSetting = FinanceSetting::findOrFail($id);

        // Combine day and month into a date format (assuming year is the current year)
        $day = str_pad($request->day, 2, '0', STR_PAD_LEFT);
        $month = array_search($request->month, [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ]) + 1; // Month is 1-based

        // Create the date in Y-m-d format
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', now()->year . '-' . $month . '-' . $day)->format('Y-m-d');

        // Save the date to the financial_year_end
        $financeSetting->financial_year_end = $date;
        $financeSetting->currency_id = $request->currency_id;
        $financeSetting->save();

        // cache()->forget('purchase_setting_' . $financeSetting->company_id);

        return Reply::success(__('messages.updateSuccess'));
    }

    public function index()
    {

        $this->accounttypes = AccountType::all();
        $this->accountcategories = AccountCategory::with('accountType')->get();
        $this->facilities = Facility::all();
        $this->services = Service::all();
        $this->pageTitle = 'accounting::app.menu.AccountingSettings';
        $this->financeSetting = FinanceSetting::first();
        $this->financeSetting->financial_year_end = Carbon::parse($this->financeSetting->financial_year_end);
        $this->currencies = Currency::all();
        $tab = request('tab');

        $this->view = match ($tab) {
            'accountcategories' => 'accounting::accounting-settings.ajax.accountcategories',
            'finacialsettings' => 'accounting::accounting-settings.ajax.general',
            default => 'accounting::accounting-settings.ajax.accounttype',
        };

        $this->activeTab = $tab ?: 'accounttypes';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('accounting::accounting-settings.index', $this->data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hotelmanagement::create');
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
        return view('hotelmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('hotelmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
