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
use Modules\DWC\Entities\DwcHotelReservation;

class DwcHotelReservationDataTable extends BaseDataTable
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
        $datatables = datatables()->eloquent($query)
            ->addColumn('check', fn ($row) => $this->checkBox($row))
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">
                        <div class="dropdown">
                            <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-options-vertical icons"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                // $action .= '<a href="' . route('hotels.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                // $action .= '<a class="dropdown-item openRightModal" href="' . route('hotels.edit', [$row->id]) . '">
                //                 <i class="fa fa-edit mr-2"></i>' . trans('app.edit') . '
                //             </a>';
                // $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                //                 <i class="fa fa-trash mr-2"></i>' . trans('app.delete') . '
                //             </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('hotel', fn ($row) => $row->hotel ? $row->hotel['name'] : 'Not Selected')
            ->editColumn('billingcode', fn ($row) => $row->billingcode ? $row->billingcode['name'] : 'Not Selected')
            ->editColumn('created_at', fn ($row) => Carbon::parse($row->created_at)->translatedFormat($this->company->date_format))
            ->addIndexColumn()
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);

        return $datatables;
    }


    /**
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DwcHotelReservation $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;
        $checkinout = $request->checkinout;
        // dd($request);
        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = $request->startDate;
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = $request->endDate;
        }
        if ($startDate && $endDate) {
            if ($checkinout === 'checkin_date') {
                $model = $model->whereBetween('dwc_hotel_reservations.checkin_date', [$startDate, $endDate]);
            } elseif ($checkinout === 'checkout_date') {
                $model = $model->whereBetween('dwc_hotel_reservations.checkout_date', [$startDate, $endDate]);
            }
        }

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $dataTable = $this->setBuilder('hotel-reservation-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["hotel-reservation-table"].buttons().container()
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
            __('app.Hotel') => ['data' => 'hotel', 'name' => 'hotel', 'title' => __('app.Hotel')],
            __('app.roomType') => ['data' => 'room_type', 'name' => 'room_type', 'title' => __('app.roomType')],
            __('app.checkin_date') => ['data' => 'checkin_date', 'name' => 'checkin_date', 'title' => __('app.checkin_date')],
            __('app.checkout_date') => ['data' => 'checkout_date', 'name' => 'checkout_date', 'title' => __('app.checkout_date')],
            __('app.noOfNights') => ['data' => 'no_of_nights', 'name' => 'no_of_nights', 'title' => __('app.noOfNights')],
            __('app.billingCode') => ['data' => 'billingcode', 'name' => 'billingcode', 'title' => __('app.billingCode')],
            __('app.ConfirmationNo') => ['data' => 'confirmation_no', 'name' => 'confirmation_no', 'title' => __('app.ConfirmationNo')],
            __('app.sharingWith') => ['data' => 'sharing_with', 'name' => 'sharing_with', 'title' => __('app.sharingWith')],
            __('app.HotelReservationNote') => ['data' => 'note_2', 'name' => 'note_2', 'title' => __('app.HotelReservationNote')],
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
