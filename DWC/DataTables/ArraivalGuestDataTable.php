<?php

namespace Modules\DWC\DataTables;

use App\Helper\Common;
use App\Models\EmployeeDetails;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use App\Models\Role;
use App\DataTables\BaseDataTable;
use App\Models\User;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Modules\DWC\Entities\DwcGuest;

class ArraivalGuestDataTable extends BaseDataTable
{
    private $editEmployeePermission;
    private $deleteEmployeePermission;
    private $viewEmployeePermission;
    private $changeEmployeeRolePermission;

    public function __construct()
    {
        parent::__construct();
        $this->editEmployeePermission = user()->permission('edit_employees');
        $this->deleteEmployeePermission = user()->permission('delete_employees');
        $this->viewEmployeePermission = user()->permission('view_employees');
        $this->changeEmployeeRolePermission = user()->permission('change_employee_role');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);
        $datatables->addColumn('check', function ($row) {
            if ($row->id != user()->id) {
                return $this->checkBox($row);
            }
            return '--';
        });

        $datatables->addColumn('name', fn ($row) => $row->first_name . ' ' . $row->last_name);

        $datatables->addColumn('arrival_date', fn ($row) => Carbon::parse($row->flightTickets()->first()?->arrival_date)->translatedFormat($this->company->date_format));

        $datatables->addColumn('arrival_time', fn ($row) => Carbon::parse($row->flightTickets()->first()?->arrival_time)->translatedFormat($this->company->time_format));

        $datatables->addColumn('flight_no', fn ($row) => $row->flightTickets()->first()->flight_no);

        $datatables->addIndexColumn();
        $datatables->setRowId(fn ($row) => 'row-' . $row->id);

        $datatables->rawColumns(['action', 'check']);

        return $datatables;
    }

    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DwcGuest $model)
    {
        $request = $this->request();

        return $model->with('flightTickets')
            ->has('flightTickets');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('guests-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["guests-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   $(".select-picker").selectpicker();
                 }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $data = [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => false],
            __('modules.guests.id') => ['data' => 'id', 'name' => 'id', 'title' => __('modules.guests.id')],
            __('modules.guests.name') => ['data' => 'name', 'name' => 'name', 'exportable' => false, 'title' => __('modules.guests.name')],
            __('modules.guests.company') => ['data' => 'company', 'name' => 'company', 'exportable' => false, 'title' => __('modules.guests.company')],
            __('modules.guests.mobile') => ['data' => 'mobile', 'name' => 'mobile', 'exportable' => false, 'title' => __('modules.guests.mobile')],
            __('modules.guests.email') => ['data' => 'email', 'name' => 'email', 'exportable' => false, 'title' => __('modules.guests.email')],
            __('modules.guests.flight_no') => ['data' => 'flight_no', 'name' => 'flight_no', 'title' => __('modules.guests.flight_no')],
            __('modules.guests.arrival_date') => ['data' => 'arrival_date', 'name' => 'arrival_date', 'title' => __('modules.guests.arrival_date')],
            __('modules.guests.arrival_time') => ['data' => 'arrival_time', 'name' => 'arrival_time', 'title' => __('modules.guests.arrival_time')],
        ];

        return $data;
    }
}
