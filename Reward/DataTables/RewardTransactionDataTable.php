<?php

namespace Modules\Reward\DataTables;

use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Reward\Entities\RewardTransaction;

class RewardTransactionDataTable extends BaseDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check', fn($row) => $this->checkBox($row))
            ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
             ->editColumn('transaction_date', function ($row) {
                return \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y');
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';
                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('reward-transactions.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>' . trans('app.delete') . '
                            </a>';

                $action .= '</div></div></div>';

                return $action;
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['check', 'action']);
    }

    public function query(RewardTransaction $model): QueryBuilder
    {
        $request = $this->request();

        $query = $model->with('customer')->newQuery();

        if ($request->searchText != '') {
            $query->where(function ($q) {
                $search = request('searchText');
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhere('transaction_type', 'like', "%$search%")
                    ->orWhere('points', 'like', "%$search%")
                    ->orWhere('reference_type', 'like', "%$search%");
            });
        }

        return $query;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('reward-transactions-table')->parameters([
            'initComplete' => 'function () {
                window.LaravelDataTables["reward-transactions-table"].buttons().container()
                .appendTo("#table-actions")
            }',
            'fnDrawCallback' => 'function( oSettings ) {
                $("#reward-transactions-table .select-picker").selectpicker();
                $(".bs-tooltip-top").removeClass("show");
            }',
        ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make([
                'extend' => 'excel',
                'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')
            ]));
        }

        return $dataTable;
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
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            'customer_name' => ['data' => 'customer_name', 'name' => 'customer.name', 'title' => 'Customer'],
            'transaction_type' => ['data' => 'transaction_type', 'name' => 'transaction_type', 'title' => 'Type'],
            'points' => ['data' => 'points', 'name' => 'points', 'title' => 'Points'],
            'reference_type' => ['data' => 'reference_type', 'name' => 'reference_type', 'title' => 'Reference Type'],
            'reference_id' => ['data' => 'reference_id', 'name' => 'reference_id', 'title' => 'Reference ID'],
            'transaction_date' => ['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Transaction Date'],
            'status' => ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            'earned_from' => ['data' => 'earned_from', 'name' => 'earned_from', 'title' => 'Earned From'],
            'remarks' => ['data' => 'remarks', 'name' => 'remarks', 'title' => 'Remarks'],

            Column::computed('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20'),
        ];
    }

    protected function filename(): string
    {
        return 'RewardTransaction_' . now()->format('YmdHis');
    }
}
