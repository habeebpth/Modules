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

class payrollSalaryCalculationDataTable extends BaseDataTable
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
            ->rawColumns(['action', 'check','employee_name']);
    }

    public function query(PayrollLeaveSalaryCalculation $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if (!is_null($request->month) && $request->month != 'null' && $request->month != '') {
            $month = $request->year.'-'.$request->month;
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
            'payroll_leave_salary_calculations.total_deduction'
        ])
            ->leftJoin('users', 'users.id', '=', 'payroll_leave_salary_calculations.employee_id');

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('salary-calculation-table')
            ->parameters(
                [
                    'scrollX' => true,
                    'initComplete' => 'function () {
                   window.LaravelDataTables["salary-calculation-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#salary-calculation-table .select-picker").selectpicker();
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
            'salary_basic' => ['data' => 'salary_basic', 'name' => 'payroll_leave_salary_calculations.salary_basic', 'title' => 'Basic'],
            'salary_spay' => ['data' => 'salary_spay', 'name' => 'payroll_leave_salary_calculations.salary_spay', 'title' => 'spay'],
            'salary_hra' => ['data' => 'salary_hra', 'name' => 'payroll_leave_salary_calculations.salary_hra', 'title' => 'HRA'],
            'salary_incentive' => ['data' => 'salary_incentive', 'name' => 'payroll_leave_salary_calculations.salary_incentive', 'title' => 'Incentive'],
            'salary_ot' => ['data' => 'salary_ot', 'name' => 'payroll_leave_salary_calculations.salary_ot', 'title' => 'OT'],
            'salary_leave' => ['data' => 'salary_leave', 'name' => 'payroll_leave_salary_calculations.salary_leave', 'title' => 'Leave<br>Salary'],
            'salary_advance' => ['data' => 'salary_advance', 'name' => 'payroll_leave_salary_calculations.salary_advance', 'title' => 'Salary<br>Advance'],
            'salary_hra_advance' => ['data' => 'salary_hra_advance', 'name' => 'payroll_leave_salary_calculations.salary_hra_advance', 'title' => ' HRA<br>Advance'],
            'salary_gross' => ['data' => 'salary_gross', 'name' => 'payroll_leave_salary_calculations.salary_gross', 'title' => 'Gross<br>Salary'],
            'total_deduction' => ['data' => 'total_deduction', 'name' => 'payroll_leave_salary_calculations.total_deduction', 'title' => 'Gros<br>Deduction'],
            'salary_net' => ['data' => 'salary_net', 'name' => 'payroll_leave_salary_calculations.salary_net', 'title' => 'Salary Net'],
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
        return 'payroll-salary-calculation_' . date('YmdHis');
    }
}
