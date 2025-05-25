<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\FiscalYear;

class FiscalYearController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Fiscal Years';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $this->fiscalYears = FiscalYear::where('company_id', user()->company_id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('accounting::fiscal-years.index', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Check for overlapping fiscal years
        $overlap = FiscalYear::where('company_id', user()->company_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlap) {
            return Reply::error('Fiscal year dates overlap with existing fiscal year');
        }

        FiscalYear::create($request->all() + ['company_id' => user()->company_id]);

        return Reply::success('Fiscal year created successfully');
    }
}
