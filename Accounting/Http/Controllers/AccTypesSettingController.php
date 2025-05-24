<?php

namespace Modules\Accounting\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\AccountType;
use Modules\HotelManagement\Entities\Floor;

class AccTypesSettingController extends AccountBaseController
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
        $accounttypes = AccountType::findOrFail($request->accounttypes_id);
        $accounttypes->disable = $request->disable;
        $accounttypes->save();

        return response()->json(['status' => 'success', 'message' => __('accounttypes status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        return view('accounting::accounting-settings.accounttypes.create-accounttypes-modal', [
            'company_id' => $this->company_id
        ]);

    }

    /**
     * @param StoreLeadSource $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'company_id'   => 'required|integer|exists:companies,id',
        ]);

        $accounttype = new AccountType();
        $accounttype->name = $request->name;
        $accounttype->description = $request->description;
        $accounttype->disable = 'y';
        $accounttype->company_id = $request->company_id;
        $accounttype->save();
        $accounttypes = AccountType::get();
        $options = BaseModel::options($accounttypes);
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
        $this->accounttypes = AccountType::findOrFail($id);
        // dd($this->accounttypes);

        return view('accounting::accounting-settings.accounttypes.edit-accounttypes-modal', $this->data);
    }

    /**
     * @param UpdateLeadSource $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
        ]);
        $accounttype = AccountType::findOrFail($id);

        $accounttype->name = $request->name;
        $accounttype->description = $request->description;
        $accounttype->disable = 'y';
        // $accounttype->company_id = $request->company_id;
        $accounttype->save();

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

        AccountType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
