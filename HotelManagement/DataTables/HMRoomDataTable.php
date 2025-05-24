<?php

namespace Modules\HotelManagement\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\HmRoom;

class HMRoomDataTable extends BaseDataTable
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
                $action .= '<a href="' . route('hm-rooms.show', [$row->id]) . '" class="dropdown-item"><i class="mr-2 fa fa-eye"></i>' . __('app.view') . '</a>';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('hm-rooms.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row-hmrooms" href="javascript:;" data-id="' . $row->id . '">
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

    public function query(HmRoom $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        if (!empty($request->startDate) && $request->startDate !== 'null') {
            $startDate = companyToDateString($request->startDate);
        }

        if (!empty($request->endDate) && $request->endDate !== 'null') {
            $endDate = companyToDateString($request->endDate);
        }
        // $leadID = $request->leadID;
        if ($startDate != null && $endDate != null) {
            $model = $model->whereBetween('hm_rooms.created_at', [$startDate, $endDate]);
        }
        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('properties.property_name', 'like', request('searchText') . '%')
                          ->orWhere('floors.floor_name', 'like', request('searchText') . '%')
                          ->orWhere('room_types.room_type_name', 'like', request('searchText') . '%')
                          ->orWhere('hm_rooms.room_no', 'like', '%' . request('searchText') . '%')
                          ->orWhere('hm_rooms.room_size', 'like', '%' . request('searchText') . '%')
                          ->orWhere('hm_rooms.no_of_beds', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // if ($leadID != 0 && $leadID != null && $leadID != 'all') {
        //     $model = $model->where('contact_person_id', '=', $leadID);
        // }
        $model = $model->select(
            'properties.property_name as property_name',
            'floors.floor_name as floor_name',
            'room_types.room_type_name as room_type_name',
            'hm_rooms.id',
            'room_no',
            'room_size',
            'no_of_beds',
            'room_description',
            'room_conditions'
        )
        ->leftJoin('properties', 'properties.id', '=', 'hm_rooms.property_id')
        ->leftJoin('floors', 'floors.id', '=', 'hm_rooms.floor_id')
        ->leftJoin('room_types', 'room_types.id', '=', 'hm_rooms.room_type_id');

        // Filter by date range
        if ($startDate && $endDate) {
            $model->whereBetween('hm_rooms.created_at', [$startDate, $endDate]);
        }

        // Search filter
        if (!empty($request->searchText)) {
            $searchText = $request->searchText;
            $model->where(function ($query) use ($searchText) {
                $query->where('properties.property_name', 'like', "$searchText%")
                    ->orWhere('floors.floor_name', 'like', "$searchText%")
                    ->orWhere('room_types.room_type_name', 'like', "$searchText%")
                    ->orWhere('hm_rooms.room_no', 'like', "%$searchText%")
                    ->orWhere('hm_rooms.room_size', 'like', "%$searchText%")
                    ->orWhere('hm_rooms.no_of_beds', 'like', "%$searchText%");
            });
        }

        // Assigned to filter
        if ($request->assignedTo != '' && $request->assignedTo != null && $request->assignedTo != 'all' && $request->assignedTo != 'unassigned') {
            $model->where('hm_rooms.property_id', $request->assignedTo);
        }

        return $model->newQuery();
    }


    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('hm-rooms-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["hm-rooms-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#hm-rooms-table .select-picker").selectpicker();
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
            'property_name' => ['data' => 'property_name', 'name' => 'property_id', 'title' => 'Property'],
            // 'facility_name' => ['data' => 'facility_name', 'name' => 'facility_id', 'title' => 'Facility '],
            'floor_name' => ['data' => 'floor_name', 'name' => 'floor_id', 'title' => 'Floor '],
            'room_type_name' => ['data' => 'room_type_name', 'name' => 'room_type_id', 'title' => 'Room Type'],
            'room_no' => ['data' => 'room_no', 'name' => 'room_no', 'title' => 'Room No'],
            'room_size' => ['data' => 'room_size', 'name' => 'room_size', 'title' => 'Room Size'],
            'no_of_beds' => ['data' => 'no_of_beds', 'name' => 'no_of_beds', 'title' => 'No Of Bed'],
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
        return 'HMRooms_' . date('YmdHis');
    }

}
