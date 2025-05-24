<?php

namespace Modules\Reward\DataTables;

use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Reward\Entities\RewardCustomer;

class RewardCustomerDataTable extends BaseDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check', fn($row) => $this->checkBox($row))
            ->addColumn('customer_id', function ($row) {
                return $row->customer->name ?? '-';
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';
                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('reward-customers.edit', [$row->id]) . '">
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

    public function query(RewardCustomer $model): QueryBuilder
    {
        $request = $this->request();

        $query = $model->with('customer')->newQuery(); // eager load customer

        if ($request->searchText != '') {
            $query->where(function ($q) {
                $search = request('searchText');
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })
                    ->orWhere('total_points_earned', 'like', "%$search%")
                    ->orWhere('total_points_redeemed', 'like', "%$search%");
            });
        }

        return $query;
    }


    public function html()
    {
        $dataTable = $this->setBuilder('reward-customers-table')->parameters([
            'initComplete' => 'function () {
                window.LaravelDataTables["reward-customers-table"].buttons().container()
                .appendTo("#table-actions")
            }',
            'fnDrawCallback' => 'function( oSettings ) {
                $("#reward-customers-table .select-picker").selectpicker();
                $(".bs-tooltip-top").removeClass("show");
            }',
        ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
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
            'customer_id' => ['data' => 'customer_id', 'name' => 'customer.name', 'title' => 'Customer Name'],
            'total_points_earned' => ['data' => 'total_points_earned', 'name' => 'total_points_earned', 'title' => 'Points Earned'],
            'total_points_redeemed' => ['data' => 'total_points_redeemed', 'name' => 'total_points_redeemed', 'title' => 'Points Redeemed'],
            'onhold_balance' => ['data' => 'onhold_balance', 'name' => 'onhold_balance', 'title' => 'On-hold Balance'],
            'current_balance' => ['data' => 'current_balance', 'name' => 'current_balance', 'title' => 'Current Balance'],

            Column::computed('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    protected function filename(): string
    {
        return 'RewardCustomer_' . now()->format('YmdHis');
    }
}
