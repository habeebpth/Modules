<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\Budget;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class BudgetDataTable extends BaseDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check', fn ($row) => $this->checkBox($row))
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';
                $action .= '<div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.budgets.edit', $row->id) . '">
                    <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';

                $action .= '<a class="dropdown-item delete-budget" href="javascript:;" data-id="' . $row->id . '">
                    <i class="fa fa-trash mr-2"></i>' . __('app.delete') . '</a>';

                $action .= '</div></div></div>';
                return $action;
            })
            ->addColumn('fiscal_year_name', function ($row) {
                return $row->fiscalYear ? $row->fiscalYear->name : '';
            })
            ->addColumn('account_code', function ($row) {
                return $row->account ? $row->account->account_code : '';
            })
            ->addColumn('account_name', function ($row) {
                return $row->account ? $row->account->account_name : '';
            })
            ->editColumn('budgeted_amount', function ($row) {
                return currency_format($row->budgeted_amount);
            })
            ->editColumn('actual_amount', function ($row) {
                return currency_format($row->actual_amount);
            })
            ->editColumn('variance', function ($row) {
                $variance = $row->budgeted_amount - $row->actual_amount;
                $class = $variance >= 0 ? 'text-success' : 'text-danger';
                return '<span class="' . $class . '">' . currency_format($variance) . '</span>';
            })
            ->addColumn('period_display', function ($row) {
                return ucfirst($row->period_type) . ' ' . $row->period_number;
            })
            ->rawColumns(['action', 'variance', 'check'])
            ->addIndexColumn();
    }

    public function query(Budget $model): QueryBuilder
    {
        $request = $this->request();

        $query = $model->where('company_id', user()->company_id)
            ->with(['fiscalYear', 'account']);

        // Fiscal year filter
        if ($request->fiscal_year_id && $request->fiscal_year_id != 'all') {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        // Account type filter
        if ($request->account_type && $request->account_type != 'all') {
            $query->whereHas('account', function($q) use ($request) {
                $q->where('account_type', $request->account_type);
            });
        }

        // Period type filter
        if ($request->period_type && $request->period_type != 'all') {
            $query->where('period_type', $request->period_type);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('budgets-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["budgets-table"].buttons().container()
                        .appendTo("#table-actions")
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            ['data' => 'fiscal_year_name', 'name' => 'fiscalYear.name', 'title' => __('Fiscal Year')],
            ['data' => 'account_code', 'name' => 'account.account_code', 'title' => __('Account Code')],
            ['data' => 'account_name', 'name' => 'account.account_name', 'title' => __('Account Name')],
            ['data' => 'period_display', 'name' => 'period_type', 'title' => __('Period')],
            ['data' => 'budgeted_amount', 'name' => 'budgeted_amount', 'title' => __('Budgeted')],
            ['data' => 'actual_amount', 'name' => 'actual_amount', 'title' => __('Actual')],
            ['data' => 'variance', 'name' => 'variance', 'title' => __('Variance')],
            Column::computed('action', __('Action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    protected function filename(): string
    {
        return 'Budgets_' . date('YmdHis');
    }
}
