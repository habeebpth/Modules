<?php

namespace Modules\Payroll\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\Property;
use Modules\Payroll\Entities\EmployeeExpense;

class PayrollEmployeeExpenseDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check', fn($row) => $this->checkBox($row))
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';

                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';
                $action .= '<a class="dropdown-item openRightModal" href="' . route('employee-expense.repayment', [$row->id]) . '">
                                <i class="fa fa-money-bill mr-2"></i>
                                ' . trans('app.repayment') . '
                            </a>';
                $action .= '<a href="' . route('employee-expense.show', [$row->id]) . $this->tabUrl . '" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('employee-expense.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-employee-expense" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);
    }

    public function query(EmployeeExpense $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = companyToDateString($request->startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = companyToDateString($request->endDate);
        }
        // $leadID = $request->leadID;
        if ($startDate != null && $endDate != null) {
            $model = $model->whereBetween('employee_expenses.created_at', [$startDate, $endDate]);
        }
        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('employee_expenses.expense_type', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.payment_mode', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.employee_id', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.expense_date', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.amount', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.details', 'like', '%' . request('searchText') . '%')
                        ->orWhere('employee_expenses.approval_date', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // if ($leadID != 0 && $leadID != null && $leadID != 'all') {
        //     $model = $model->where('contact_person_id', '=', $leadID);
        // }
        $model = $model->select([

            'u1.name as added_by_name',
            'u2.name as last_updated_by_name',
            'u3.name as employee_name',
            'employee_expenses.id',
            'employee_expenses.employee_id',
            'employee_expenses.expense_type',
            'employee_expenses.payment_mode',
            'employee_expenses.expense_date',
            'employee_expenses.amount',
            'employee_expenses.details',
            'employee_expenses.approval_status',
            'employee_expenses.created_at',
            'employee_expenses.updated_at',
            'employee_expenses.added_by',
            'employee_expenses.last_updated_by',
            'employee_expenses.approved_by',
            'employee_expenses.approval_date',
            'employee_expenses.salary_recovery',
            'employee_expenses.repayment_status',
            'employee_expenses.repayment_method',
            'employee_expenses.transaction_reference'
        ])
            ->leftJoin('users as u1', 'u1.id', '=', 'employee_expenses.added_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'employee_expenses.last_updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'employee_expenses.employee_id');

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('employee-expense-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["employee-expense-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#employee-expense-table .select-picker").selectpicker();
                    $(".bs-tooltip-top").removeClass("show");
                    $(".select-picker.change-status").each(function() {
                        var selectPicker = $(this);
                        selectPicker.selectpicker();
                        selectPicker.siblings("button").attr("title", "");
                    });
                }',
                ]
            );

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
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
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            'expense_type' => ['data' => 'expense_type', 'name' => 'expense_type', 'title' => 'Expense Type'],
            'payment_mode' => ['data' => 'payment_mode', 'name' => 'payment_mode', 'title' => 'Payment Mode'],
            'employee_id' => ['data' => 'employee_name', 'name' => 'u3.name', 'title' => '    Employee'],
            'added_by' => ['data' => 'added_by_name', 'name' => 'u1.name', 'title' => 'Added By'], // Fixed
            'last_updated_by' => ['data' => 'last_updated_by_name', 'name' => 'u2.name', 'title' => 'Last Updated By'], // Fixed
            'expense_date' => ['data' => 'expense_date', 'name' => 'expense_date', 'title' => 'Expense Date'],
            'amount' => ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            'details' => ['data' => 'details', 'name' => 'details', 'title' => 'Details'],
            'approval_status' => ['data' => 'approval_status', 'name' => 'approval_status', 'title' => 'Approval Status'],
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
        return 'payroll-employee-expense_' . date('YmdHis');
    }
}
