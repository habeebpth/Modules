<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\TaxCode;
use Modules\Accounting\Entities\ChartOfAccount;

class TaxCodeController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Tax Codes';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->taxCodes = TaxCode::where('company_id', user()->company_id)
            ->with('taxAccount')
            ->get();

        return view('accounting::tax-codes.index', $this->data);
    }

    public function create()
    {
        $this->pageTitle = 'Create Tax Code';
        $this->taxAccounts = ChartOfAccount::where('company_id', user()->company_id)
            ->where('account_type', 'liability')
            ->active()
            ->get();

        return view('accounting::tax-codes.create', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:tax_codes,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:sales,purchase,both',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        TaxCode::create($request->all() + ['company_id' => user()->company_id]);

        return Reply::successWithData('Tax code created successfully', [
            'redirectUrl' => route('accounting.tax-codes.index')
        ]);
    }
}
