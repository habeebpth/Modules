<?php

namespace Modules\Accounting\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadSource;
use App\Http\Requests\LeadSetting\UpdateLeadSource;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\AccountCategory;
use Modules\Accounting\Entities\AccountType;
use Illuminate\Validation\Rule;
use Modules\HotelManagement\Entities\RoomType;

class AccCategoriesSettingController extends AccountBaseController
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
        $accountcategories = AccountCategory::findOrFail($request->accountcategories_id);
        $accountcategories->disable = $request->disable;
        $accountcategories->save();

        return response()->json(['status' => 'success', 'message' => __('accountcategories status updated successfully')]);
    }

    public function create()
    {
        $this->company_id = user()->company_id;
        $this->accountTypes = AccountType::get();
        return view('accounting::accounting-settings.accountcategories.create-accountcategories-modal', $this->data);

    }

    /**
     * @param StoreLeadSource $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:account_categories,code',
            'description' => 'nullable|string',
            'company_id'     => 'required|integer|exists:companies,id',
        ]);


        $AccountCategory = new AccountCategory();
        $AccountCategory->account_type_id = $request->account_type_id;
        $AccountCategory->name = $request->name;
        $AccountCategory->code = $request->code;
        $AccountCategory->description = $request->description;
        $AccountCategory->disable = 'y';
        $AccountCategory->company_id = $request->company_id;
        $AccountCategory->save();
        $AccountCategories = AccountCategory::get();
        $options = BaseModel::options($AccountCategories);
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
        $this->accountCategory = AccountCategory::findOrFail($id);
        // dd($this->floor);
        $this->accountTypes = AccountType::get();
        return view('accounting::accounting-settings.accountcategories.edit-accountcategories-modal', $this->data);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'name' => 'required|string|max:255',
            'code' => ['required','string',Rule::unique('account_categories', 'code')->ignore($id),
],
            'description' => 'nullable|string',
        ]);
        $AccountCategory = AccountCategory::findOrFail($id);

        $AccountCategory->account_type_id = $request->account_type_id;
        $AccountCategory->name = $request->name;
        $AccountCategory->code = $request->code;
        $AccountCategory->description = $request->description;
        $AccountCategory->disable = 'y';
        $AccountCategory->save();

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

        AccountCategory::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
