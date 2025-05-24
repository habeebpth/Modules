<?php

namespace Modules\Accounting\DataTables;

use App\Models\CustomField;
use Illuminate\Support\Facades\Log;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Accounting\Entities\Account;

class AccountsDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
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
                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounts.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>' . trans('app.edit') . '</a>';
                $action .= '<a class="dropdown-item delete-table-row-accounts" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>' . trans('app.delete') . '</a>';
                $action .= '</div>';

                return $action;
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);
    }

    public function query(Account $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = $request->startDate ? companyToDateString($request->startDate) : null;
        $endDate = $request->endDate ? companyToDateString($request->endDate) : null;

        $query = $model->newQuery();

        if ($startDate && $endDate) {
            $query->whereBetween('accounts.created_at', [$startDate, $endDate]);
        }

        if ($request->searchText) {
            $query->where(function ($query) {
                $query->where('accounts.name', 'like', '%' . request('searchText') . '%')
                      ->orWhere('accounts.code', 'like', '%' . request('searchText') . '%')
                      ->orWhere('accounts.description', 'like', '%' . request('searchText') . '%')
                      ->orWhere('account_categories.name', 'like', '%' . request('searchText') . '%');

            });
        }

        return $query->select(
            'account_categories.name as account_categories',
            'accounts.id', // Explicitly alias id
            'accounts.name',
            'accounts.code',
            'accounts.description',
            'accounts.company_id',
            'accounts.account_parent_id'
        )->leftJoin('account_categories', 'account_categories.id', '=', 'accounts.account_category_id');
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('accounts-table')
            ->parameters([
                'initComplete' => 'function () {
                     window.LaravelDataTables["accounts-table"].buttons().container().appendTo("#table-actions");
                 }',
                'fnDrawCallback' => 'function() {
                     $("#accounts-table .select-picker").selectpicker();
                     $(".bs-tooltip-top").removeClass("show");
                 }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(
                Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')])
            );
        }

        return $dataTable;
    }


    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => '#'],
            'name' => ['data' => 'name', 'name' => 'accounts.name', 'title' => 'Name'],
            'code' => ['data' => 'code', 'name' => 'accounts.code', 'title' => 'Code'],
            'account_categories' => ['data' => 'account_categories', 'name' => 'account_categories.name', 'title' => 'Account Categories'],
            'description' => ['data' => 'description', 'name' => 'accounts.description', 'title' => 'Description'],
            Column::computed('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Accounts_' . date('YmdHis');
    }

}
