<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\AccountingSetting;

class AccountingSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Accounting Settings';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->settings = [
            'auto_post_journals' => AccountingSetting::getSetting('auto_post_journals', false),
            'require_journal_reference' => AccountingSetting::getSetting('require_journal_reference', false),
            'allow_future_dates' => AccountingSetting::getSetting('allow_future_dates', true),
            'default_cash_account' => AccountingSetting::getSetting('default_cash_account'),
            'default_ar_account' => AccountingSetting::getSetting('default_ar_account'),
            'default_ap_account' => AccountingSetting::getSetting('default_ap_account'),
        ];

        return view('accounting::settings.index', $this->data);
    }

    public function update(Request $request)
    {
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            AccountingSetting::setSetting($key, $value);
        }

        return Reply::success('Settings updated successfully');
    }
}
