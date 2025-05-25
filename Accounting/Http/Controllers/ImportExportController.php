<?php
namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Accounting\Services\ImportExportService;

class ImportExportController extends AccountBaseController
{
    protected $importExportService;

    public function __construct(ImportExportService $importExportService)
    {
        parent::__construct();
        $this->importExportService = $importExportService;
        $this->pageTitle = 'Import/Export';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accounting', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        return view('accounting::import-export.index', $this->data);
    }

    public function importChartOfAccounts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);

        try {
            $result = $this->importExportService->importChartOfAccounts($request->file('file'));
            return Reply::successWithData('Accounts imported successfully', $result);
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }

    public function exportChartOfAccounts()
    {
        try {
            return $this->importExportService->exportChartOfAccounts();
        } catch (\Exception $e) {
            return Reply::error($e->getMessage());
        }
    }
}
