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
use Modules\Payroll\Entities\PayrollSalaryAdvance;

class PayrollSalaryAdvanceDataTable extends BaseDataTable
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
                $action .= '<a class="dropdown-item openRightModal" href="' . route('salary-advance.repayment', [$row->id]) . '">
                                <i class="fa fa-money-bill mr-2"></i>
                                ' . trans('app.repayment') . '
                            </a>';
                $action .= '<a href="' . route('salary-advance.show', [$row->id]) . $this->tabUrl .'" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('salary-advance.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-salary-advance" href="javascript:;" data-id="' . $row->id . '">
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
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns([ 'action','check']);
    }

    public function query(PayrollSalaryAdvance $model): QueryBuilder
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
            $model = $model->whereBetween('payroll_salary_advance.created_at', [$startDate, $endDate]);
        }
        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('payroll_salary_advance.advance_type', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.payment_mode', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.employee_id', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.request_date', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.amount', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.reason', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_salary_advance.approval_status', 'like', '%' . request('searchText') . '%');
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
            'payroll_salary_advance.id',
            'payroll_salary_advance.employee_id',
            'payroll_salary_advance.advance_type',
            'payroll_salary_advance.payment_mode',
            'payroll_salary_advance.request_date',
            'payroll_salary_advance.amount',
            'payroll_salary_advance.reason',
            'payroll_salary_advance.approval_status',
            'payroll_salary_advance.created_at',
            'payroll_salary_advance.updated_at',
            'payroll_salary_advance.added_by',
            'payroll_salary_advance.last_updated_by',
            'payroll_salary_advance.approved_by',
            'payroll_salary_advance.disbursement_date',
            'payroll_salary_advance.approval_date',
            'payroll_salary_advance.repayment_status',
            'payroll_salary_advance.repayment_method',
            'payroll_salary_advance.transaction_reference'
        ])
        ->leftJoin('users as u1', 'u1.id', '=', 'payroll_salary_advance.added_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'payroll_salary_advance.last_updated_by')
        ->leftJoin('users as u3', 'u3.id', '=', 'payroll_salary_advance.employee_id');

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('salary-advance-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["salary-advance-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#salary-advance-table .select-picker").selectpicker();
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
            'advance_type' => ['data' => 'advance_type', 'name' => 'advance_type', 'title' => 'Advance Type'],
            'payment_mode' => ['data' => 'payment_mode', 'name' => 'payment_mode', 'title' => 'Payment Mode'],
            'employee_id' => ['data' => 'employee_name', 'name' => 'u3.name', 'title' => '    Employee'],
           'added_by' => ['data' => 'added_by_name', 'name' => 'u1.name', 'title' => 'Added By'], // Fixed
        'last_updated_by' => ['data' => 'last_updated_by_name', 'name' => 'u2.name', 'title' => 'Last Updated By'], // Fixed
            'request_date' => ['data' => 'request_date', 'name' => 'request_date', 'title' => 'Request Date'],
            'amount' => ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            'reason' => ['data' => 'reason', 'name' => 'reason', 'title' => 'Reason'],
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
        return 'payroll-salary-advance_' . date('YmdHis');
    }

}
