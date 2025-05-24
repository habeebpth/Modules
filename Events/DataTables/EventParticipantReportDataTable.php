<?php

namespace Modules\Events\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Modules\Events\Entities\EventCheckinPoint;
use Yajra\DataTables\EloquentDataTable;
use App\Models\GlobalSetting;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Events\Entities\EventParticipant;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\Property;

class EventParticipantReportDataTable extends BaseDataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $EventId = $this->request->input('EventId');

        return (new EloquentDataTable($query))
            ->addColumn('check', fn ($row) => $this->checkBox($row))
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);
    }

    public function query(EventParticipant $model): QueryBuilder
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;
        $EventId = $request->EventId;
        if (!empty($request->startDate) && $request->startDate !== 'null') {
            $startDate = companyToDateString($request->startDate);
        }

        if (!empty($request->endDate) && $request->endDate !== 'null') {
            $endDate = companyToDateString($request->endDate);
        }

        // Initialize query builder and join evnt_events
        $query = $model->newQuery()
            ->selectRaw('
                evnt_events.name as event_name,
                COUNT(event_participants.id) as total_participants,
                COUNT(CASE WHEN event_registrations.sex = "male" THEN 1 END) as total_male,
                COUNT(CASE WHEN event_registrations.sex = "female" THEN 1 END) as total_female,
                SUM(CASE WHEN event_registrations.kids_under_12 > 0 THEN 1 END) as total_kids
            ')
            ->join('evnt_events', 'evnt_events.id', '=', 'event_participants.event_id')
            ->leftJoin('event_registrations', 'event_registrations.id', '=', 'event_participants.event_registration_id')
            ->groupBy('event_participants.event_id');

        // Apply event ID filter if applicable
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $query->where('event_participants.event_id', '=', $EventId);
        }
        $query->where('event_participants.checkin_point_id', EventCheckinPoint::where('name', 'entrance')->first()?->id ?? 1);

        // Apply date range filter if both start and end dates are provided
        if ($startDate && $endDate) {
            $query->whereBetween('event_participants.created_at', [$startDate, $endDate]);
        }

        return $query;
    }




    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('event-participant-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["event-participant-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#event-participant-table .select-picker").selectpicker();
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
            'event_name' => ['data' => 'event_name', 'name' => 'event_name', 'title' => 'Event'],
            'total_participants' => ['data' => 'total_participants', 'name' => 'total_participants', 'title' => 'Total Participants'],
            'total_male' => ['data' => 'total_male', 'name' => 'total_male', 'title' => 'Total Male'],
            'total_female' => ['data' => 'total_female', 'name' => 'total_female', 'title' => 'Total Female'],
            'total_kids' => ['data' => 'total_kids', 'name' => 'total_kids', 'title' => 'Total Kids']
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Eventparticipants_' . date('YmdHis');
    }
}
