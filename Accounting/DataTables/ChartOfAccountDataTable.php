<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\ChartOfAccount;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ChartOfAccountDataTable extends BaseDataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';
                $action .= '<div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';
                
                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.chart-of-accounts.edit', $row->id) . '">
                    <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';
                
                $action .= '<a class="dropdown-item delete-account" href="javascript:;" data-id="' . $row->id . '">
                    <i class="fa fa-trash mr-2"></i>' . __('app.delete') . '</a>';
                
                $action .= '</div></div></div>';
                return $action;
            })
            ->editColumn('account_type', function ($row) {
                return ucfirst($row->account_type);
            })
            ->editColumn('current_balance', function ($row) {
                return currency_format($row->current_balance);
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
            })
            ->rawColumns(['action', 'is_active']);
    }

    public function query(ChartOfAccount $model)
    {
        return $model->where('company_id', user()->company_id)
            ->orderBy('account_code');
    }

    public function html()
    {
        return $this->setBuilder('chart-of-accounts-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["chart-of-accounts-table"].buttons().container()
                        .appendTo("#table-actions")
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'account_code', 'name' => 'account_code', 'title' => 'Code'],
            ['data' => 'account_name', 'name' => 'account_name', 'title' => 'Account Name'],
            ['data' => 'account_type', 'name' => 'account_type', 'title' => 'Type'],
            ['data' => 'current_balance', 'name' => 'current_balance', 'title' => 'Balance'],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => 'Status'],
            Column::computed('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }
}