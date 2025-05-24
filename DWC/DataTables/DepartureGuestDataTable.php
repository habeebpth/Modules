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

class GuestsDataTable extends BaseDataTable
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

        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

            $action .= '<a href="' . route('employees.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

            // if (
            //     $this->editEmployeePermission == 'all'
            //     || ($this->editEmployeePermission == 'added' && user()->id == $row->added_by)
            //     || ($this->editEmployeePermission == 'owned' && user()->id == $row->id)
            //     || ($this->editEmployeePermission == 'both' && (user()->id == $row->id || user()->id == $row->added_by))
            // ) {
            //     if (!in_array('admin', $userRole) || (in_array('admin', $userRole) && in_array('admin', user_roles()))) {
            //         $action .= '<a class="dropdown-item openRightModal" href="' . route('employees.edit', [$row->id]) . '">
            //                     <i class="fa fa-edit mr-2"></i>
            //                     ' . trans('app.edit') . '
            //                 </a>';
            //     }
            // }

            // if ($this->deleteEmployeePermission == 'all' || ($this->deleteEmployeePermission == 'added' && user()->id == $row->added_by)) {
            //     if ((!in_array('admin', $userRole) && user()->id !== $row->id) || (user()->id !== $row->id && in_array('admin', $userRole) && in_array('admin', user_roles()))) {
            //         $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
            //                     <i class="fa fa-trash mr-2"></i>
            //                     ' . trans('app.delete') . '
            //                 </a>';
            //     }
            // }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });
        $datatables->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->translatedFormat($this->company->date_format));
        $datatables->editColumn('horse', fn ($row) => $row->horse ? $row->horse['name'] : 'Not Selected');

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

        return $model->newQuery();
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
            __('modules.guests.first_name') => ['data' => 'first_name', 'name' => 'first_name', 'exportable' => false, 'title' => __('modules.guests.first_name')],
            __('modules.guests.last_name') => ['data' => 'last_name', 'name' => 'last_name', 'exportable' => false, 'title' => __('modules.guests.last_name')],
            __('modules.guests.horse') => ['data' => 'horse', 'name' => 'horse', 'exportable' => false, 'title' => __('modules.guests.horse')],
            __('modules.guests.company') => ['data' => 'company', 'name' => 'company', 'exportable' => false, 'title' => __('modules.guests.company')],
            __('modules.guests.country') => ['data' => 'country', 'name' => 'country', 'exportable' => false, 'title' => __('modules.guests.country')],
            __('modules.guests.mobile') => ['data' => 'mobile', 'name' => 'mobile', 'exportable' => false, 'title' => __('modules.guests.mobile')],
            __('modules.guests.email') => ['data' => 'email', 'name' => 'email', 'exportable' => false, 'title' => __('modules.guests.email')],
            __('modules.guests.visa_required') => ['data' => 'visa_required', 'name' => 'visa_required', 'exportable' => false, 'title' => __('modules.guests.visa_required')]
        ];

        $action = [
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

        return array_merge($data, $action);
    }
}
