<?php

namespace Modules\Payroll\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\EmployeeDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Payroll\Entities\PayrollSetting;
use Modules\Payroll\Entities\ExpenseType;

class PayrollExpenseTypeSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(PayrollSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        return view('payroll::expense-type.create-expensetype-modal');
    }

    /**
     * @param StoreExpenseType $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:191',
        ]);

        $ExpenseType = new ExpenseType();
        $ExpenseType->type = $request->type;
        $ExpenseType->company_id = user()->company_id;
        $ExpenseType->added_by = auth()->id();
        $ExpenseType->last_updated_by = auth()->id();
        $ExpenseType->save();

        // $ExpenseType = ExpenseType::get();
        // $options = BaseModel::options($ExpenseType);
        // return Reply::successWithData(__('messages.recordSaved'), ['data' => $options]);
        return Reply::success(__('messages.recordSaved'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $this->expensetypes = ExpenseType::findOrFail($id);

        return view('payroll::expense-type.edit-expensetype-modal', $this->data);
    }

    /**
     * @param UpdateExpenseType $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:191',
        ]);

        $ExpenseType = ExpenseType::findOrFail($id);

        // Check for duplicate type within the same company, excluding current ID
        $exists = ExpenseType::where('company_id', user()->company_id)
            ->where('type', $request->type)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return Reply::error(__('This document type already exists for your company.'));
        }

        $ExpenseType->type = $request->type;
        $ExpenseType->last_updated_by = auth()->id();
        $ExpenseType->save();

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
        // $type = ExpenseType::findOrFail($id);

        ExpenseType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }
}
