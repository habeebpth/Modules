<?php

namespace Modules\Payroll\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Helper\Files;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Support\Facades\DB;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HotelManagement\Entities\Property;
use Modules\HotelManagement\DataTables\HmPropertiesDataTable;
use Modules\Payroll\DataTables\PayrollSalaryAdvanceDataTable;
use Modules\Payroll\Entities\PayrollSalaryAdvance;
use Modules\Payroll\Entities\PayrollSalaryAdvanceRepayment;
use Modules\Payroll\Entities\PayrollFiles;
use Modules\Payroll\Entities\SalaryPaymentMethod;

class PayrollSalaryAdvanceController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.PayrollSalaryAdvance';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index(PayrollSalaryAdvanceDataTable $dataTable)
    {

        $this->Properties = PayrollSalaryAdvance::get();
        return $dataTable->render('payroll::payroll-salary-advance.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function repaymentCreate($id)
    {

        $this->pageTitle = __('app.advanceSalaryRepayment');
        $this->company_id = user()->company_id;
        $this->salaryadvance = PayrollSalaryAdvance::findOrFail($id);
        $this->repayment = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $id)->get();
        $totalRepaid = $this->repayment->sum('amount');
        $this->Pendingamount = $this->salaryadvance->amount - $totalRepaid;
        $this->salary_advance_id = $id;
        $this->employees = User::allEmployees();
        $this->view = 'payroll::payroll-salary-advance.repayment.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-salary-advance.create', $this->data);
    }
    public function repaymentStore(Request $request)
    {
        // Validate request
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'salary_advance_id' => 'required|integer|exists:payroll_salary_advance,id',
            'due_date' => 'required|date',
            'paid_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'repayment_method' => 'required|in:One-time,Installments',
            'payment_mode' => 'required|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'transaction_reference' => 'nullable|string|max:50|unique:payroll_salary_advance_repayment,transaction_reference',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Fetch salary advance details
            $PayrollSalaryAdvance = PayrollSalaryAdvance::findOrFail($request->salary_advance_id);
            $this->repayment = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $request->salary_advance_id)->get();
            $totalRepaid = $this->repayment->sum('amount');
            $Pendingamount = $PayrollSalaryAdvance->amount - $totalRepaid;
            // Determine repayment status
            if ($request->amount >= $Pendingamount) {
                $PayrollSalaryAdvance->repayment_status = 'Completed';
            } else {
                $PayrollSalaryAdvance->repayment_status = 'Pending';
            }
            $PayrollSalaryAdvance->repayment_method = $request->repayment_method;
            $PayrollSalaryAdvance->save();

            // Determine installment number
            $installment_no = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $request->salary_advance_id)->count() + 1;

            // Create a new repayment entry
            $payrollAdvanceRepayment = new PayrollSalaryAdvanceRepayment();
            $payrollAdvanceRepayment->company_id = $request->company_id;
            $payrollAdvanceRepayment->salary_advance_id = $request->salary_advance_id;
            $payrollAdvanceRepayment->installment_no = $installment_no;
            $payrollAdvanceRepayment->due_date = companyToYmd($request->due_date);
            $payrollAdvanceRepayment->paid_date = companyToYmd($request->paid_date);
            $payrollAdvanceRepayment->amount = $request->amount;
            $payrollAdvanceRepayment->payment_mode = $request->payment_mode;
            $payrollAdvanceRepayment->payment_status = 'Paid'; // Assuming payment is completed
            $payrollAdvanceRepayment->transaction_reference = $request->transaction_reference;
            $payrollAdvanceRepayment->added_by = user()->id;
            $payrollAdvanceRepayment->last_updated_by = user()->id;
            $payrollAdvanceRepayment->save();

            // Handle File Upload
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = time() . '_' . $file->getClientOriginalName();
                $storedFilename = Files::uploadLocalOrS3($file, PayrollFiles::FILE_PATH . '/' . $payrollAdvanceRepayment->id);

                // Save file details in payroll_files table
                $PayrollFiles = new PayrollFiles();
                $PayrollFiles->company_id = $request->company_id;
                $PayrollFiles->advance_repayment_id = $payrollAdvanceRepayment->id;
                $PayrollFiles->filename = $filename;
                $PayrollFiles->hashname = $storedFilename;
                $PayrollFiles->size = $file->getSize();
                $PayrollFiles->added_by = user()->id;
                $PayrollFiles->last_updated_by = user()->id;
                $PayrollFiles->save();
            }

            DB::commit();

            return Reply::successWithData(__('messages.payrollAdvanceRepaymentAddedSuccessfully'), [
                'redirectUrl' => route('salary-advance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error($e->getMessage());
        }
    }
    public function repaymentEdit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.advanceSalaryRepayment');

        $this->repayment = PayrollSalaryAdvanceRepayment::with('files')->findOrFail($id);

        $this->salaryadvance = PayrollSalaryAdvance::findOrFail($this->repayment->salary_advance_id);
        $this->repayments = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $this->repayment->salary_advance_id)->get();
        $totalRepaid = $this->repayments->sum('amount');
        $this->Pendingamount = $this->salaryadvance->amount - $totalRepaid;
        $this->company_id = user()->company_id;
        $this->view = 'payroll::payroll-salary-advance.repayment.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-salary-advance.create', $this->data);
    }
    public function repaymentUpdate(Request $request, $id)
    {

        // dd($request->all());
        // Validate request
        $request->validate([
            'due_date' => 'required|date',
            'paid_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'repayment_method' => 'required|in:One-time,Installments',
            'payment_mode' => 'required|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'transaction_reference' => 'nullable|string|max:50|unique:payroll_salary_advance_repayment,transaction_reference,' . $id,
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Fetch the existing repayment record
            $payrollAdvanceRepayment = PayrollSalaryAdvanceRepayment::findOrFail($id);
            $salary_advance_id = $payrollAdvanceRepayment->salary_advance_id;
            $PayrollSalaryAdvance = PayrollSalaryAdvance::findOrFail($salary_advance_id);

            // Calculate the correct pending amount before updating
            $totalRepaid = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $salary_advance_id)
                ->where('id', '!=', $id) // Exclude current repayment
                ->sum('amount');

            $Pendingamount = $PayrollSalaryAdvance->amount - $totalRepaid;

            if ($request->amount >= $Pendingamount) {
                $PayrollSalaryAdvance->repayment_status = 'Completed';
            }
            $PayrollSalaryAdvance->repayment_method = $request->repayment_method;
            $PayrollSalaryAdvance->save();

            // Update repayment details
            $payrollAdvanceRepayment->due_date = companyToYmd($request->due_date);
            $payrollAdvanceRepayment->paid_date = companyToYmd($request->paid_date);
            $payrollAdvanceRepayment->amount = $request->amount;
            $payrollAdvanceRepayment->payment_mode = $request->payment_mode;
            $payrollAdvanceRepayment->transaction_reference = $request->transaction_reference;
            $payrollAdvanceRepayment->last_updated_by = user()->id;
            $payrollAdvanceRepayment->save();

            // Handle File Upload
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = time() . '_' . $file->getClientOriginalName();
                $storedFilename = Files::uploadLocalOrS3($file, PayrollFiles::FILE_PATH . '/' . $payrollAdvanceRepayment->id);

                // Delete old file if exists
                PayrollFiles::where('advance_repayment_id', $id)->delete();

                // Save new file details in payroll_files table
                $PayrollFiles = new PayrollFiles();
                $PayrollFiles->company_id = $request->company_id;
                $PayrollFiles->advance_repayment_id = $payrollAdvanceRepayment->id;
                $PayrollFiles->filename = $filename;
                $PayrollFiles->hashname = $storedFilename;
                $PayrollFiles->size = $file->getSize();
                $PayrollFiles->added_by = user()->id;
                $PayrollFiles->last_updated_by = user()->id;
                $PayrollFiles->save();
            }

            DB::commit();

            return Reply::successWithData(__('messages.payrollAdvanceRepaymentUpdatedSuccessfully'), [
                'redirectUrl' => route('salary-advance.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error($e->getMessage());
        }
    }

    public function create()
    {

        $this->pageTitle = __('app.advanceSalary');
        $this->company_id = user()->company_id;
        $this->employees = User::allEmployees();
        $this->paymentMethods = SalaryPaymentMethod::where('company_id', $this->company_id)->get();

        $this->view = 'payroll::payroll-salary-advance.ajax.create';


        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-salary-advance.ajax.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());  // For debugging

        // Validate request
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'advance_type' => 'required|string',
            'employee_id' => 'required|integer|exists:users,id',
            'request_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
            'approval_status' => 'required|string|in:Pending,Approved,Rejected',
            'approved_by' => 'nullable|string|max:255',
            'approval_date' => 'nullable|date',
            'disbursement_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_mode' => 'required|string|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'repayment_method' => 'required|string|in:One-time,Installments',
            // 'repayment_status' => 'required|string|in:Pending,Completed',
        ]);

        // Handle approval details
        if ($request->approval_status === 'Approved') {
            $approvedby = user()->id;
            $approvaldate = $request->approval_date ? companyToYmd($request->approval_date) : now();
        } else {
            $approvedby = null;
            $approvaldate = null;
        }

        // Create PayrollSalaryAdvance record
        $payrollAdvance = new PayrollSalaryAdvance();
        $payrollAdvance->company_id = $request->company_id;
        $payrollAdvance->advance_type = $request->advance_type;
        $payrollAdvance->employee_id = $request->employee_id;

        // Ensure request_date exists and is stored correctly
        if ($request->has('request_date')) {
            $payrollAdvance->request_date = companyToYmd($request->request_date);
        }

        $payrollAdvance->amount = $request->amount;
        $payrollAdvance->reason = $request->reason;
        $payrollAdvance->approval_status = $request->approval_status;
        $payrollAdvance->approved_by = $approvedby;
        $payrollAdvance->added_by = user()->id;
        $payrollAdvance->last_updated_by = user()->id;
        $payrollAdvance->approval_date = $approvaldate;

        if ($request->has('disbursement_date')) {
            $payrollAdvance->disbursement_date = companyToYmd($request->disbursement_date);
        }

        $payrollAdvance->transaction_reference = $request->transaction_reference;
        $payrollAdvance->payment_mode = $request->payment_mode;
        $payrollAdvance->repayment_method = $request->repayment_method;
        $payrollAdvance->repayment_status = 'Pending';
        if ($request->repayment_method === 'Installments') {
            $payrollAdvance->number_of_installments = $request->number_of_installments;
            $payrollAdvance->deduct_from_salary = $request->deduct_from_salary;
        }
        $payrollAdvance->save();

        // Save repayment details if installments exist
        if ($request->repayment_method === 'Installments' && isset($request->installments)) {
            foreach ($request->installments as $index => $installment) {
                // dd($installment['date']);
                // For each installment, use 'date' and 'amount' inside the array
                $repayment = new PayrollSalaryAdvanceRepayment();
                $repayment->company_id = $request->company_id;
                $repayment->salary_advance_id = $payrollAdvance->id;
                $repayment->installment_no = $index + 1;  // Installment number starts from 1
                $repayment->due_date = $installment['date'];  // Access the date
                $repayment->paid_date = null;
                $repayment->amount = (float)$installment['amount'];  // Access the amount
                $repayment->transaction_reference = null;
                $repayment->payment_mode = $request->payment_mode;
                $repayment->payment_status = 'Pending';
                $repayment->added_by = user()->id;
                $repayment->last_updated_by = user()->id;
                $repayment->save();
            }
        }

        return Reply::successWithData(__('messages.payrollAdvanceAddedSuccessfully'), [
            'redirectUrl' => route('salary-advance.index')
        ]);
    }

    public function show($id)
    {
        $this->pageTitle = __('app.AdvanceDetail');
        $this->salaryadvance = PayrollSalaryAdvance::with('employeeuser')->findOrFail($id);
        $this->advancerepayment = PayrollSalaryAdvanceRepayment::where('salary_advance_id', $id)->with('addedby', 'lastupdatedby')->get();
        $this->view = 'payroll::payroll-salary-advance.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-salary-advance.create', $this->data);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.advanceSalary');

        $this->payrollAdvance = PayrollSalaryAdvance::with('installments')->findOrFail($id);
        $this->company_id = user()->company_id;
        $this->paymentMethods = SalaryPaymentMethod::where('company_id', $this->company_id)->get();
        $this->countries = countries();
        $this->employees = User::allEmployees();

        $this->view = 'payroll::payroll-salary-advance.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-salary-advance.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'advance_type' => 'required|string',
            'employee_id' => 'required|integer|exists:users,id',
            'request_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
            'approval_status' => 'required|string|in:Pending,Approved,Rejected',
            'approved_by' => 'nullable|string|max:255',
            'approval_date' => 'nullable|date',
            'disbursement_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_mode' => 'required|string|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'repayment_method' => 'required|string|in:One-time,Installments',
        ]);

        // Find existing record
        $payrollAdvance = PayrollSalaryAdvance::findOrFail($id);

        // Handle approval info
        if ($request->approval_status === 'Approved') {
            $approvedby = user()->id;
            $approvaldate = $request->approval_date ? companyToYmd($request->approval_date) : now();
        } else {
            $approvedby = null;
            $approvaldate = null;
        }

        $payrollAdvance->company_id = user()->company_id;
        $payrollAdvance->advance_type = $request->advance_type;
        $payrollAdvance->employee_id = $request->employee_id;

        if ($request->has('request_date')) {
            $payrollAdvance->request_date = companyToYmd($request->request_date);
        }

        $payrollAdvance->amount = $request->amount;
        $payrollAdvance->reason = $request->reason;
        $payrollAdvance->approval_status = $request->approval_status;
        $payrollAdvance->approved_by = $approvedby;
        $payrollAdvance->last_updated_by = user()->id;
        $payrollAdvance->approval_date = $approvaldate;
        $payrollAdvance->disbursement_date = $request->disbursement_date ? companyToYmd($request->disbursement_date) : null;
        $payrollAdvance->transaction_reference = $request->transaction_reference;
        $payrollAdvance->payment_mode = $request->payment_mode;
        $payrollAdvance->repayment_method = $request->repayment_method;

        $payrollAdvance->repayment_status = 'Pending'; // You might want to preserve old status conditionally

        if ($request->repayment_method === 'Installments') {
            $payrollAdvance->number_of_installments = $request->number_of_installments;
            $payrollAdvance->deduct_from_salary = $request->deduct_from_salary;
        } else {
            $payrollAdvance->number_of_installments = null;
            $payrollAdvance->deduct_from_salary = null;
        }

        $payrollAdvance->save();

        // Delete old repayment records before re-adding
        PayrollSalaryAdvanceRepayment::where('salary_advance_id', $payrollAdvance->id)->delete();

        // Re-add new installments
        if ($request->repayment_method === 'Installments' && isset($request->installments)) {
            foreach ($request->installments as $index => $installment) {
                $repayment = new PayrollSalaryAdvanceRepayment();
                $repayment->company_id = user()->company_id;
                $repayment->salary_advance_id = $payrollAdvance->id;
                $repayment->installment_no = $index + 1;
                $repayment->due_date = $installment['date'];
                $repayment->paid_date = null;
                $repayment->amount = (float) $installment['amount'];
                $repayment->transaction_reference = null;
                $repayment->payment_mode = $request->payment_mode;
                $repayment->payment_status = 'Pending';
                $repayment->added_by = user()->id;
                $repayment->last_updated_by = user()->id;
                $repayment->save();
            }
        }

        return Reply::successWithData(__('messages.payrollAdvanceUpdatedSuccessfully'), [
            'redirectUrl' => route('salary-advance.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    // Find the EmployeeExpense record
    $expense = PayrollSalaryAdvance::findOrFail($id);

    // Delete related repayments first
    PayrollSalaryAdvanceRepayment::where('salary_advance_id', $expense->id)->delete();

    // Then delete the main expense record
    $expense->delete();

    return Reply::successWithData(__('messages.payrollSalaryAdvanceDeletedSuccessfully'), [
        'redirectUrl' => route('salary-advance.index')
    ]);
}

}
