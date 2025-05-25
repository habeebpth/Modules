<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\Reconciliation;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ReconciliationDataTable extends BaseDataTable
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

                $action .= '<a href="' . route('accounting.reconciliations.show', $row->id) . '" class="dropdown-item">
                    <i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($row->status === 'draft') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.reconciliations.edit', $row->id) . '">
                        <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';
                }

                $action .= '</div></div></div>';
                return $action;
            })
            ->addColumn('account_name', function ($row) {
                return $row->account ? $row->account->account_name : '';
            })
            ->editColumn('reconciliation_date', function ($row) {
                return $row->reconciliation_date ? $row->reconciliation_date->format(company()->date_format) : '';
            })
            ->editColumn('statement_balance', function ($row) {
                return currency_format($row->statement_balance);
            })
            ->editColumn('book_balance', function ($row) {
                return currency_format($row->book_balance);
            })
            ->editColumn('difference', function ($row) {
                $class = $row->difference == 0 ? 'text-success' : 'text-danger';
                return '<span class="' . $class . '">' . currency_format($row->difference) . '</span>';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match($row->status) {
                    'draft' => 'badge-warning',
                    'completed' => 'badge-success',
                    'reviewed' => 'badge-primary',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['action', 'difference', 'status', 'check'])
            ->addIndexColumn();
    }

    public function query(Reconciliation $model): QueryBuilder
    {
        return $model->where('company_id', user()->company_id)
            ->with(['account'])
            ->orderBy('reconciliation_date', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('reconciliations-table');
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
            ['data' => 'account_name', 'name' => 'account.account_name', 'title' => __('Account')],
            ['data' => 'reconciliation_date', 'name' => 'reconciliation_date', 'title' => __('Date')],
            ['data' => 'statement_balance', 'name' => 'statement_balance', 'title' => __('Statement Balance')],
            ['data' => 'book_balance', 'name' => 'book_balance', 'title' => __('Book Balance')],
            ['data' => 'difference', 'name' => 'difference', 'title' => __('Difference')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status')],
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
        return 'Reconciliations_' . date('YmdHis');
    }
}
