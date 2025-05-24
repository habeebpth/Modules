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
use Modules\Payroll\DataTables\PayrollEmployeeExpenseDataTable;
use Modules\Payroll\Entities\EmployeeExpense;
use Modules\Payroll\Entities\PayrollEmployeeExpenseRepayment;
use Modules\Payroll\Entities\PayrollFiles;
use Modules\Payroll\Entities\SalaryPaymentMethod;

class PayrollEmployeeExpenseController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.PayrollEmployeeExpense';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }
    public function index(PayrollEmployeeExpenseDataTable $dataTable)
    {

        $this->Properties = EmployeeExpense::get();
        return $dataTable->render('payroll::payroll-employee-expense.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function repaymentCreate($id)
    {

        $this->pageTitle = __('app.EmployeeExpenseRepayment');
        $this->company_id = user()->company_id;
        $this->EmployeeExpense = EmployeeExpense::findOrFail($id);
        $this->repayment = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $id)->get();
        $totalRepaid = $this->repayment->sum('amount');
        $this->Pendingamount = $this->EmployeeExpense->amount - $totalRepaid;
        $this->employee_expense_id = $id;
        $this->employees = User::allEmployees();
        $this->view = 'payroll::payroll-employee-expense.repayment.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-employee-expense.create', $this->data);
    }
    public function repaymentStore(Request $request)
    {
        // Validate request
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'employee_expense_id' => 'required|integer|exists:employee_expenses,id',
            'due_date' => 'required|date',
            'paid_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'repayment_method' => 'required|in:One-time,Installments',
            'payment_mode' => 'required|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'transaction_reference' => 'nullable|string|max:50|unique:payroll_employee_expense_repayments,transaction_reference',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Fetch salary advance details
            $PayrollEmployeeExpense = EmployeeExpense::findOrFail($request->employee_expense_id);
            $this->repayment = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $request->employee_expense_id)->get();
            $totalRepaid = $this->repayment->sum('amount');
            $Pendingamount = $PayrollEmployeeExpense->amount - $totalRepaid;
            // Determine repayment status
            if ($request->amount >= $Pendingamount) {
                $PayrollEmployeeExpense->repayment_status = 'Completed';
            } else {
                $PayrollEmployeeExpense->repayment_status = 'Pending';
            }
            $PayrollEmployeeExpense->repayment_method = $request->repayment_method;
            $PayrollEmployeeExpense->save();

            // Determine installment number
            $installment_no = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $request->employee_expense_id)->count() + 1;

            // Create a new repayment entry
            $payrollAdvanceRepayment = new PayrollEmployeeExpenseRepayment();
            $payrollAdvanceRepayment->company_id = $request->company_id;
            $payrollAdvanceRepayment->employee_expense_id = $request->employee_expense_id;
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
                $PayrollFiles->employee_expense_repayment_id = $payrollAdvanceRepayment->id;
                $PayrollFiles->filename = $filename;
                $PayrollFiles->hashname = $storedFilename;
                $PayrollFiles->size = $file->getSize();
                $PayrollFiles->added_by = user()->id;
                $PayrollFiles->last_updated_by = user()->id;
                $PayrollFiles->save();
            }

            DB::commit();

            return Reply::successWithData(__('messages.EmployeeExpenseRepaymentAddedSuccessfully'), [
                'redirectUrl' => route('employee-expense.index')
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

        $this->repayment = PayrollEmployeeExpenseRepayment::with('files')->findOrFail($id);

        $this->EmployeeExpense = EmployeeExpense::findOrFail($this->repayment->employee_expense_id);
        $this->repayments = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $this->repayment->employee_expense_id)->get();
        $totalRepaid = $this->repayments->sum('amount');
        $this->Pendingamount = $this->EmployeeExpense->amount - $totalRepaid;
        $this->company_id = user()->company_id;
        $this->view = 'payroll::payroll-employee-expense.repayment.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-employee-expense.create', $this->data);
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
            'transaction_reference' => 'nullable|string|max:50|unique:payroll_employee_expense_repayments,transaction_reference,' . $id,
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Fetch the existing repayment record
            $payrollAdvanceRepayment = PayrollEmployeeExpenseRepayment::findOrFail($id);
            $employee_expense_id = $payrollAdvanceRepayment->employee_expense_id;
            $PayrollEmployeeExpense = EmployeeExpense::findOrFail($employee_expense_id);

            // Calculate the correct pending amount before updating
            $totalRepaid = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $employee_expense_id)
                ->where('id', '!=', $id) // Exclude current repayment
                ->sum('amount');

            $Pendingamount = $PayrollEmployeeExpense->amount - $totalRepaid;

            if ($request->amount >= $Pendingamount) {
                $PayrollEmployeeExpense->repayment_status = 'Completed';
            }
            $PayrollEmployeeExpense->repayment_method = $request->repayment_method;
            $PayrollEmployeeExpense->save();

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
                $PayrollFiles->employee_expense_repayment_id = $payrollAdvanceRepayment->id;
                $PayrollFiles->filename = $filename;
                $PayrollFiles->hashname = $storedFilename;
                $PayrollFiles->size = $file->getSize();
                $PayrollFiles->added_by = user()->id;
                $PayrollFiles->last_updated_by = user()->id;
                $PayrollFiles->save();
            }

            DB::commit();

            return Reply::successWithData(__('messages.EmployeeExpenseRepaymentUpdatedSuccessfully'), [
                'redirectUrl' => route('employee-expense.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error($e->getMessage());
        }
    }

    public function create()
    {

        $this->pageTitle = __('app.addemployeeExpense');
        $this->company_id = user()->company_id;
        $this->employees = User::allEmployees();
        $this->paymentMethods = SalaryPaymentMethod::where('company_id', $this->company_id)->get();

        $this->view = 'payroll::payroll-employee-expense.ajax.create';


        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-employee-expense.ajax.create', $this->data);
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
        'expense_type' => 'required|string',
        'employee_id' => 'required|integer|exists:users,id',
        'expense_date' => 'required|date',
        'amount' => 'required|numeric|min:1',
        'details' => 'nullable|string',
        'approval_status' => 'required|string|in:Pending,Approved,Rejected',
        'approved_by' => 'nullable|string|max:255',
        'approval_date' => 'nullable|date',
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

    // Create PayrollEmployeeExpense record
    $payrollAdvance = new EmployeeExpense();
    $payrollAdvance->company_id = $request->company_id;
    $payrollAdvance->expense_type = $request->expense_type;
    $payrollAdvance->employee_id = $request->employee_id;

    // Ensure expense_date exists and is stored correctly
    if ($request->has('expense_date')) {
        $payrollAdvance->expense_date = companyToYmd($request->expense_date);
    }

    $payrollAdvance->amount = $request->amount;
    $payrollAdvance->details = $request->details;
    $payrollAdvance->approval_status = $request->approval_status;
    $payrollAdvance->approved_by = $approvedby;
    $payrollAdvance->added_by = user()->id;
    $payrollAdvance->last_updated_by = user()->id;
    $payrollAdvance->approval_date = $approvaldate;

    // if ($request->has('disbursement_date')) {
    //     $payrollAdvance->disbursement_date = companyToYmd($request->disbursement_date);
    // }

    $payrollAdvance->transaction_reference = $request->transaction_reference;
    $payrollAdvance->payment_mode = $request->payment_mode;
    $payrollAdvance->repayment_method = $request->repayment_method;
    $payrollAdvance->repayment_status = 'Pending';
    if ($request->repayment_method === 'Installments') {
        $payrollAdvance->no_instalments = $request->number_of_installments;
        $payrollAdvance->salary_recovery = $request->salary_recovery;
    }
    $payrollAdvance->save();

    // Save repayment details if installments exist
    if ($request->repayment_method === 'Installments' && isset($request->installments)) {
        foreach ($request->installments as $index => $installment) {
            // dd($installment['date']);
            // For each installment, use 'date' and 'amount' inside the array
            $repayment = new PayrollEmployeeExpenseRepayment();
            $repayment->company_id = $request->company_id;
            $repayment->employee_expense_id = $payrollAdvance->id;
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

    return Reply::successWithData(__('messages.payrollEmployeeExpenseAddedSuccessfully'), [
        'redirectUrl' => route('employee-expense.index')
    ]);
}

    public function show($id)
    {
        $this->pageTitle = __('app.EmployeeExpenseDetails');
        $this->EmployeeExpense = EmployeeExpense::with('employeeuser')->findOrFail($id);
        $this->advancerepayment = PayrollEmployeeExpenseRepayment::where('employee_expense_id', $id)->with('addedby', 'lastupdatedby')->get();
        $this->view = 'payroll::payroll-employee-expense.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-employee-expense.create', $this->data);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        // dd($id);
        $this->pageTitle = __('app.editEmloyeeExpense');

        $this->payrollAdvance = EmployeeExpense::with('installments')->findOrFail($id);
        $this->company_id = user()->company_id;
        $this->paymentMethods = SalaryPaymentMethod::where('company_id', $this->company_id)->get();
        $this->countries = countries();
        $this->employees = User::allEmployees();

        $this->view = 'payroll::payroll-employee-expense.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('payroll::payroll-employee-expense.create', $this->data);
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'expense_type' => 'required|string',
            'employee_id' => 'required|integer|exists:users,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'details' => 'nullable|string',
            'approval_status' => 'required|string|in:Pending,Approved,Rejected',
            'approved_by' => 'nullable|string|max:255',
            'approval_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_mode' => 'required|string|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'repayment_method' => 'required|string|in:One-time,Installments',
        ]);

        // Find existing record
        $payrollAdvance = EmployeeExpense::findOrFail($id);

        // Handle approval info
        if ($request->approval_status === 'Approved') {
            $approvedby = user()->id;
            $approvaldate = $request->approval_date ? companyToYmd($request->approval_date) : now();
        } else {
            $approvedby = null;
            $approvaldate = null;
        }

        $payrollAdvance->company_id = user()->company_id;
        $payrollAdvance->expense_type = $request->expense_type;
        $payrollAdvance->employee_id = $request->employee_id;

        if ($request->has('expense_date')) {
            $payrollAdvance->expense_date = companyToYmd($request->expense_date);
        }

        $payrollAdvance->amount = $request->amount;
        $payrollAdvance->details = $request->details;
        $payrollAdvance->approval_status = $request->approval_status;
        $payrollAdvance->approved_by = $approvedby;
        $payrollAdvance->last_updated_by = user()->id;
        $payrollAdvance->approval_date = $approvaldate;
        $payrollAdvance->transaction_reference = $request->transaction_reference;
        $payrollAdvance->payment_mode = $request->payment_mode;
        $payrollAdvance->repayment_method = $request->repayment_method;

        $payrollAdvance->repayment_status = 'Pending'; // You might want to preserve old status conditionally

        if ($request->repayment_method === 'Installments') {
            $payrollAdvance->no_instalments = $request->number_of_installments;
            $payrollAdvance->salary_recovery = $request->salary_recovery;
        } else {
            $payrollAdvance->no_instalments = null;
            $payrollAdvance->salary_recovery = null;
        }

        $payrollAdvance->save();

        // Delete old repayment records before re-adding
        PayrollEmployeeExpenseRepayment::where('employee_expense_id', $payrollAdvance->id)->delete();

        // Re-add new installments
        if ($request->repayment_method === 'Installments' && isset($request->installments)) {
            foreach ($request->installments as $index => $installment) {
                $repayment = new PayrollEmployeeExpenseRepayment();
                $repayment->company_id = user()->company_id;
                $repayment->employee_expense_id = $payrollAdvance->id;
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

        return Reply::successWithData(__('messages.payrollEmployeeExpenseUpdatedSuccessfully'), [
            'redirectUrl' => route('employee-expense.index')
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the EmployeeExpense record
        $expense = EmployeeExpense::findOrFail($id);

        // Delete related repayments first
        PayrollEmployeeExpenseRepayment::where('employee_expense_id', $expense->id)->delete();

        // Then delete the main expense record
        $expense->delete();

        return Reply::successWithData(__('messages.payrollEmployeeExpenseDeletedSuccessfully'), [
            'redirectUrl' => route('employee-expense.index')
        ]);
    }

}
