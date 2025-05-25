<?php
// =======================
// FILE: Accounting/DataTables/FiscalYearDataTable.php
// =======================

namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\FiscalYear;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class FiscalYearDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.fiscal-years.edit', $row->id) . '">
                    <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';

                if (!$row->is_closed) {
                    $action .= '<a class="dropdown-item close-fiscal-year" href="javascript:;" data-id="' . $row->id . '">
                        <i class="fa fa-lock mr-2"></i>' . __('Close Year') . '</a>';
                }

                $action .= '</div></div></div>';
                return $action;
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date ? $row->start_date->format(company()->date_format) : '';
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date ? $row->end_date->format(company()->date_format) : '';
            })
            ->editColumn('is_active', function ($row) {
                $status = $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                if ($row->is_closed) {
                    $status .= ' <span class="badge badge-danger ml-1">Closed</span>';
                }
                return $status;
            })
            ->rawColumns(['action', 'is_active', 'check'])
            ->addIndexColumn();
    }

    public function query(FiscalYear $model): QueryBuilder
    {
        return $model->where('company_id', user()->company_id)
            ->orderBy('start_date', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('fiscal-years-table');
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'start_date', 'name' => 'start_date', 'title' => __('Start Date')],
            ['data' => 'end_date', 'name' => 'end_date', 'title' => __('End Date')],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => __('Status')],
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
        return 'FiscalYears_' . date('YmdHis');
    }
}
