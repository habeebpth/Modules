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
use Modules\Payroll\Entities\PayrollLeaveSalaryCalculation;

class SalaryAttentenceLeaveCalculationDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item openRightModal" href="' . route('salary-calculation.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row-properties" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('employee_name', fn ($row) => $row->employee_id ? view('components.employee', ['user' => $row->employee]) : '--')

            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check', 'employee_name']);
    }

    public function query(PayrollLeaveSalaryCalculation $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if (!is_null($request->month) && $request->month != 'null' && $request->month != '') {
            $month = $request->year . '-' . $request->month;
            $model = $model->where('month', $month);
        }
        if (!is_null($request->year) && $request->year != 'null' && $request->year != '') {
            $model = $model->where('year', $request->year);
        }

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
                    $query->where('users.name', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.employee_type', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.employee_grade', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.no_of_days_in_month', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.no_of_months_in_year', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot1_hrs', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot1_rate', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot1_amt', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot2_hrs', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot2_rate', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot2_amt', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot_total_hrs', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.ot_total_amt', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.days_worked', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.remarks', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.comments', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.added_by', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.updated_by', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.sl_full_pay', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.sl_half_pay', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.taken_leave', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.absent', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.combo_offs', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.total_leave_earned', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.opening_leave_balance', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.closing_leave_balance', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.opening_excess_leave', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.closing_excess_leave', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.excess_leave_taken', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_basic', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_spay', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_hra', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_incentive', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_gross', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_net', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_leave', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_advance', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_hra_advance', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.salary_ot', 'like', '%' . request('searchText') . '%')
                        ->orWhere('payroll_leave_salary_calculations.total_deduction', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // if ($leadID != 0 && $leadID != null && $leadID != 'all') {
        //     $model = $model->where('contact_person_id', '=', $leadID);
        // }
        $model = $model->select([
            'users.name as employee_name',
            'payroll_leave_salary_calculations.id',
            'payroll_leave_salary_calculations.employee_id',
            'payroll_leave_salary_calculations.month',
            'payroll_leave_salary_calculations.year',
            'payroll_leave_salary_calculations.no_of_days_in_month',
            'payroll_leave_salary_calculations.no_of_months_in_year',
            'payroll_leave_salary_calculations.employee_type',
            'payroll_leave_salary_calculations.employee_grade',
            'payroll_leave_salary_calculations.salary_basic',
            'payroll_leave_salary_calculations.salary_spay',
            'payroll_leave_salary_calculations.salary_hra',
            'payroll_leave_salary_calculations.salary_incentive',
            'payroll_leave_salary_calculations.salary_gross',
            'payroll_leave_salary_calculations.salary_net',
            'payroll_leave_salary_calculations.salary_leave',
            'payroll_leave_salary_calculations.salary_advance',
            'payroll_leave_salary_calculations.salary_hra_advance',
            'payroll_leave_salary_calculations.salary_ot',
            'payroll_leave_salary_calculations.total_deduction',
            'payroll_leave_salary_calculations.sl_full_pay',
            'payroll_leave_salary_calculations.sl_half_pay',
            'payroll_leave_salary_calculations.taken_leave',
            'payroll_leave_salary_calculations.absent',
            'payroll_leave_salary_calculations.combo_offs',
            'payroll_leave_salary_calculations.total_leave_earned',
            'payroll_leave_salary_calculations.opening_leave_balance',
            'payroll_leave_salary_calculations.closing_leave_balance',
            'payroll_leave_salary_calculations.opening_excess_leave',
            'payroll_leave_salary_calculations.closing_excess_leave',
            'payroll_leave_salary_calculations.excess_leave_taken',
            'payroll_leave_salary_calculations.ot1_hrs',
            'payroll_leave_salary_calculations.ot1_rate',
            'payroll_leave_salary_calculations.ot1_amt',
            'payroll_leave_salary_calculations.ot2_hrs',
            'payroll_leave_salary_calculations.ot2_rate',
            'payroll_leave_salary_calculations.ot2_amt',
            'payroll_leave_salary_calculations.ot_total_hrs',
            'payroll_leave_salary_calculations.ot_total_amt',
            'payroll_leave_salary_calculations.days_worked',
            'payroll_leave_salary_calculations.remarks',
            'payroll_leave_salary_calculations.comments',
            'payroll_leave_salary_calculations.added_by',
            'payroll_leave_salary_calculations.updated_by'
        ])
            ->leftJoin('users', 'users.id', '=', 'payroll_leave_salary_calculations.employee_id');

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('salary-attentence-leave-calculation-table')
            ->parameters(
                [
                    'scrollX' => true,
                    'initComplete' => 'function () {
                   window.LaravelDataTables["salary-attentence-leave-calculation-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#salary-attentence-leave-calculation-table .select-picker").selectpicker();
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
            'employee_id' => ['data' => 'employee_name', 'name' => 'users.name', 'title' => 'Employee'],
            'employee_type' => ['data' => 'employee_type', 'name' => 'payroll_leave_salary_calculations.employee_type', 'title' => 'Employee<br>Type'],
            'employee_grade' => ['data' => 'employee_grade', 'name' => 'payroll_leave_salary_calculations.employee_grade', 'title' => 'Employee<br>Grade'],
            'month' => ['data' => 'month', 'name' => 'payroll_leave_salary_calculations.month', 'title' => 'Month'],
            'year' => ['data' => 'year', 'name' => 'payroll_leave_salary_calculations.year', 'title' => 'Year'],
            'no_of_days_in_month' => ['data' => 'no_of_days_in_month', 'name' => 'payroll_leave_salary_calculations.no_of_days_in_month', 'title' => 'Days in<br>Month'],
            'no_of_months_in_year' => ['data' => 'no_of_months_in_year', 'name' => 'payroll_leave_salary_calculations.no_of_months_in_year', 'title' => 'Months in<br>Year'],
            'sl_full_pay' => ['data' => 'sl_full_pay', 'name' => 'payroll_leave_salary_calculations.sl_full_pay', 'title' => 'Sick<br>Leave<br>Full Pay'],
            'sl_half_pay' => ['data' => 'sl_half_pay', 'name' => 'payroll_leave_salary_calculations.sl_half_pay', 'title' => 'Sick<br>Leave<br>Half Pay'],
            'taken_leave' => ['data' => 'taken_leave', 'name' => 'payroll_leave_salary_calculations.taken_leave', 'title' => 'Taken<br>Leave'],
            'absent' => ['data' => 'absent', 'name' => 'payroll_leave_salary_calculations.absent', 'title' => 'Absent'],
            'combo_offs' => ['data' => 'combo_offs', 'name' => 'payroll_leave_salary_calculations.combo_offs', 'title' => 'Combo<br>Offs'],
            'total_leave_earned' => ['data' => 'total_leave_earned', 'name' => 'payroll_leave_salary_calculations.total_leave_earned', 'title' => 'Total<br>Leave<br>Earned'],
            'opening_leave_balance' => ['data' => 'opening_leave_balance', 'name' => 'payroll_leave_salary_calculations.opening_leave_balance', 'title' => 'Opening<br>Leave<br>Balance'],
            'closing_leave_balance' => ['data' => 'closing_leave_balance', 'name' => 'payroll_leave_salary_calculations.closing_leave_balance', 'title' => 'Closing<br>Leave<br>Balance'],
            'opening_excess_leave' => ['data' => 'opening_excess_leave', 'name' => 'payroll_leave_salary_calculations.opening_excess_leave', 'title' => 'Opening<br>Excess<br>Leave'],
            'closing_excess_leave' => ['data' => 'closing_excess_leave', 'name' => 'payroll_leave_salary_calculations.closing_excess_leave', 'title' => 'Closing<br>Excess<br>Leave'],
            'excess_leave_taken' => ['data' => 'excess_leave_taken', 'name' => 'payroll_leave_salary_calculations.excess_leave_taken', 'title' => 'Excess<br>Leave<br>Taken'],
            'salary_basic' => ['data' => 'salary_basic', 'name' => 'payroll_leave_salary_calculations.salary_basic', 'title' => 'Basic'],
            'salary_spay' => ['data' => 'salary_spay', 'name' => 'payroll_leave_salary_calculations.salary_spay', 'title' => 'spay'],
            'salary_hra' => ['data' => 'salary_hra', 'name' => 'payroll_leave_salary_calculations.salary_hra', 'title' => 'HRA'],
            'salary_incentive' => ['data' => 'salary_incentive', 'name' => 'payroll_leave_salary_calculations.salary_incentive', 'title' => 'Incentive'],
            'salary_gross' => ['data' => 'salary_gross', 'name' => 'payroll_leave_salary_calculations.salary_gross', 'title' => 'Gross'],
            'salary_net' => ['data' => 'salary_net', 'name' => 'payroll_leave_salary_calculations.salary_net', 'title' => 'Salary<br>Net'],
            'salary_leave' => ['data' => 'salary_leave', 'name' => 'payroll_leave_salary_calculations.salary_leave', 'title' => 'Leave<br>Salary'],
            'salary_advance' => ['data' => 'salary_advance', 'name' => 'payroll_leave_salary_calculations.salary_advance', 'title' => ' Advance'],
            'salary_hra_advance' => ['data' => 'salary_hra_advance', 'name' => 'payroll_leave_salary_calculations.salary_hra_advance', 'title' => ' HRA<br>Advance'],
            'salary_ot' => ['data' => 'salary_ot', 'name' => 'payroll_leave_salary_calculations.salary_ot', 'title' => 'OT'],
            'total_deduction' => ['data' => 'total_deduction', 'name' => 'payroll_leave_salary_calculations.total_deduction', 'title' => 'Total<br>Deduction'],
            'ot1_hrs' => ['data' => 'ot1_hrs', 'name' => 'payroll_leave_salary_calculations.ot1_hrs', 'title' => 'OT1 Hrs'],
            'ot1_rate' => ['data' => 'ot1_rate', 'name' => 'payroll_leave_salary_calculations.ot1_rate', 'title' => 'OT1 Rate'],
            'ot1_amt' => ['data' => 'ot1_amt', 'name' => 'payroll_leave_salary_calculations.ot1_amt', 'title' => 'OT1 Amount'],
            'ot2_hrs' => ['data' => 'ot2_hrs', 'name' => 'payroll_leave_salary_calculations.ot2_hrs', 'title' => 'OT2 Hrs'],
            'ot2_rate' => ['data' => 'ot2_rate', 'name' => 'payroll_leave_salary_calculations.ot2_rate', 'title' => 'OT2 Rate'],
            'ot2_amt' => ['data' => 'ot2_amt', 'name' => 'payroll_leave_salary_calculations.ot2_amt', 'title' => 'OT2 Amount'],
            'ot_total_hrs' => ['data' => 'ot_total_hrs', 'name' => 'payroll_leave_salary_calculations.ot_total_hrs', 'title' => 'Total<br>OT Hrs'],
            'ot_total_amt' => ['data' => 'ot_total_amt', 'name' => 'payroll_leave_salary_calculations.ot_total_amt', 'title' => 'Total<br>OT Amount'],
            'days_worked' => ['data' => 'days_worked', 'name' => 'payroll_leave_salary_calculations.days_worked', 'title' => 'Days<br>Worked'],
            'remarks' => ['data' => 'remarks', 'name' => 'payroll_leave_salary_calculations.remarks', 'title' => 'Remarks'],
            'comments' => ['data' => 'comments', 'name' => 'payroll_leave_salary_calculations.comments', 'title' => 'Comments'],
            'added_by' => ['data' => 'added_by', 'name' => 'payroll_leave_salary_calculations.added_by', 'title' => 'Added By'],
            'updated_by' => ['data' => 'updated_by', 'name' => 'payroll_leave_salary_calculations.updated_by', 'title' => 'Updated By'],
            // Column::computed('action', 'Action')
            //     ->exportable(false)
            //     ->printable(false)
            //     ->orderable(false)
            //     ->searchable(false)
            //     ->addClass('text-right pr-20')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'payroll-salary-calculation_' . date('YmdHis');
    }
}
