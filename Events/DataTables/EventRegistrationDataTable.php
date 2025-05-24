<?php

namespace Modules\Events\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Yajra\DataTables\EloquentDataTable;
use App\Models\GlobalSetting;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Events\Entities\EventRegistration;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\Property;

class EventRegistrationDataTable extends BaseDataTable
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
            ->addColumn('allowed_seat', function ($row) {
                if (!is_null($row->allotted_seats_start) && !is_null($row->allotted_seats_end)) {
                    return implode(', ', range($row->allotted_seats_start, $row->allotted_seats_end));
                }
                return 'N/A';
            })
            ->addColumn('action', function ($row) use ($EventId) {
                $editRoute = $EventId && $EventId !== 'all'
                    ? route('event.event-registration.edit', [$row->id])
                    : route('event-registration.edit', [$row->id]);

                return '<div class="task_view">
                            <div class="dropdown">
                                <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                   data-toggle="dropdown">
                                    <i class="icon-options-vertical icons"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item openRightModal" href="' . $editRoute . '">
                                        <i class="fa fa-edit mr-2"></i> ' . trans('app.edit') . '
                                    </a>
                                    <a class="dropdown-item delete-table-row-event-registration" href="javascript:;"
                                       data-id="' . $row->id . '">
                                        <i class="fa fa-trash mr-2"></i> ' . trans('app.delete') . '
                                    </a>
                                </div>
                            </div>
                        </div>';
            })
            ->editColumn('mobile', fn ($row) => '+' . $row->country_phonecode . ' ' . $row->mobile)
            ->editColumn('created_at', fn ($row) => companyToDateString(Carbon::parse($row->created_at)->format('d-m-Y')))
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check']);
    }

    public function query(EventRegistration $model): QueryBuilder
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
            ->select('event_registrations.*', 'evnt_events.name as event_name')
            ->join('evnt_events', 'evnt_events.id', '=', 'event_registrations.event_id');

        // Apply event ID filter if applicable
        if ($EventId != 0 && $EventId != null && $EventId != 'all') {
            $query->where('event_registrations.event_id', '=', $EventId);
        }

        // Apply date range filter if both start and end dates are provided
        if ($startDate && $endDate) {
            $query->whereBetween('event_registrations.created_at', [$startDate, $endDate]);
        }

        // Apply search filter
        $searchText = $request->searchText ?? '';
        if (!empty($searchText)) {
            $query->where(function ($q) use ($searchText) {
                $q->where('event_registrations.name', 'like', "%$searchText%")
                  ->orWhere('event_registrations.mobile', 'like', "%$searchText%")
                  ->orWhere('evnt_events.name', 'like', "%$searchText%");
            });
        }

        return $query;
    }




    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('event-registration-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["event-registration-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#event-registration-table .select-picker").selectpicker();
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
            'name' => ['data' => 'name', 'name' => 'name', 'title' => 'Student Name'],
            'event_name' => ['data' => 'event_name', 'name' => 'event_name', 'title' => 'Event'],
            'registration_code' => ['data' => 'registration_code', 'name' => 'registration_code', 'title' => 'Registration Code'],
            'mobile' => ['data' => 'mobile', 'name' => 'mobile', 'title' => 'Mobile'],
            'created_at' => ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
            'allowed_seat' => ['data' => 'allowed_seat', 'name' => 'allowed_seat', 'title' => 'Allowed Seats'],
            'no_of_participants' => ['data' => 'no_of_participants', 'name' => 'no_of_participants', 'title' => 'No Of Participants'],
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
        return 'EventRegistrations_' . date('YmdHis');
    }

}
