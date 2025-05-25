<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\TaxCode;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class TaxCodeDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.tax-codes.edit', $row->id) . '">
                    <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';

                $action .= '<a class="dropdown-item delete-tax-code" href="javascript:;" data-id="' . $row->id . '">
                    <i class="fa fa-trash mr-2"></i>' . __('app.delete') . '</a>';

                $action .= '</div></div></div>';
                return $action;
            })
            ->addColumn('tax_account_name', function ($row) {
                return $row->taxAccount ? $row->taxAccount->account_name : '-';
            })
            ->editColumn('rate', function ($row) {
                return $row->rate . '%';
            })
            ->editColumn('type', function ($row) {
                return ucfirst($row->type);
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
            })
            ->rawColumns(['action', 'is_active', 'check'])
            ->addIndexColumn();
    }

    public function query(TaxCode $model): QueryBuilder
    {
        return $model->where('company_id', user()->company_id)
            ->with(['taxAccount'])
            ->orderBy('code');
    }

    public function html()
    {
        return $this->setBuilder('tax-codes-table');
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
            ['data' => 'code', 'name' => 'code', 'title' => __('Code')],
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'type', 'name' => 'type', 'title' => __('Type')],
            ['data' => 'rate', 'name' => 'rate', 'title' => __('Rate')],
            ['data' => 'tax_account_name', 'name' => 'taxAccount.account_name', 'title' => __('Tax Account')],
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
        return 'TaxCodes_' . date('YmdHis');
    }
}
