<?php

namespace Modules\Events\DataTables;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Modules\Events\Entities\EventCheckinPoint;
use Modules\Events\Entities\EventParticipant;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Utilities\State;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class CheckedInParticipantsReportDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('registration_code', function ($row) {
                return $row->registration_code;
            })
            ->addColumn('date', function ($row) {
                return Carbon::parse($row->checkin_time)->format(company()->date_format);
            })
            ->addColumn('status', function ($row) {
                return $row->checkout_time ? "Check Out" : "Check In";
            })
            ->addColumn('checkin_time', function ($row) {
                return $row->checkin_time ? \Carbon\Carbon::parse($row->checkin_time)->format(company()->time_format) : '--';
            })
            ->addColumn('checkout_time', function ($row) {
                return $row->checkout_time ? \Carbon\Carbon::parse($row->checkout_time)->format(company()->time_format) : '--';
            })
            ->addColumn('total_spend_time', function ($row) {
                // Calculate time difference between checkin_time and current time if checkout_time is null
                if ($row->checkin_time && ! $row->checkout_time) {
                    $checkinTime = \Carbon\Carbon::parse($row->checkin_time);
                    $currentTime = \Carbon\Carbon::now();
                    $diff = $currentTime->diff($checkinTime);
                    return $diff->format('%H:%I:%S');
                } elseif ($row->checkin_time && $row->checkout_time) {
                    $checkinTime = \Carbon\Carbon::parse($row->checkin_time);
                    $checkoutTime = \Carbon\Carbon::parse($row->checkout_time);
                    $diff = $checkoutTime->diff($checkinTime);
                    return $diff->format('%H:%I:%S');
                }
                return '--';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventParticipant $model): QueryBuilder
    {
        $request = $this->request();
        $EventId = $request->EventId;

        $startDate = $request->filled('startDate') && $request->startDate !== 'null'
            ? Carbon::parse(companyToDateString($request->startDate))->startOfDay()->toDateTimeString()
            : Carbon::today()->startOfDay()->toDateTimeString();
        $endDate = $request->filled('endDate') && $request->endDate !== 'null'
            ? Carbon::parse(companyToDateString($request->endDate))->endOfDay()->toDateTimeString()
            : Carbon::today()->endOfDay()->toDateTimeString();

        $entranceCheckinId = EventCheckinPoint::where('name', 'Entrance')->first()?->id ?? 1;
        $exitCheckinId = EventCheckinPoint::where('name', 'Exit')->first()?->id ?? 2;

        $query = $model->newQuery()
            ->select([
                'event_r.name',
                'event_r.registration_code',
                'event_participants.checkin_time as checkin_time',
                'exitLogs.checkin_time       as checkout_time',
            ])
            ->leftJoin('event_participants as exitLogs', function ($join) use ($exitCheckinId) {
                $join->on('event_participants.event_registration_id', '=', 'exitLogs.event_registration_id')
                    ->where('exitLogs.checkin_point_id', $exitCheckinId);
            })
            ->join('event_registrations as event_r', 'event_r.id', '=', 'event_participants.event_registration_id')
            ->where('event_participants.checkin_point_id', $entranceCheckinId)
            ->where(function ($query) {
                $query->whereRaw('exitLogs.created_at > event_participants.created_at')
                    ->orWhereNull('exitLogs.created_at');
            })
            ->when($EventId && $EventId !== 'all', fn ($q) => $q->where('event_participants.event_id', $EventId))
            ->whereBetween('event_participants.created_at', [$startDate, $endDate]);

        $searchText = $request->searchText ?? '';
        if (! empty($searchText)) {
            $query->where(function ($q) use ($searchText) {
                $q->where('event_r.name', 'like', "%$searchText%")
                    ->orWhere('event_r.mobile', 'like', "%$searchText%")
                    ->orWhere('event_r.registration_code', 'like', "%$searchText%");
            });
        }


        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('checked-in-participants-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> '.trans('app.exportExcel')]))
            ->language(
                [
                    'url' => '//cdn.datatables.net/plug-ins/1.10.21/i18n/'.user()->locale.'.json',
                    'processing' => '<i class="fa fa-spinner fa-spin"></i> Processing Data',
                ]
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["checked-in-participants-table"].buttons().container()
                    .appendTo("#table-actions");
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title(__('app.id'))->orderable(false)->searchable(false),
            Column::make('name')->title(__('app.name')),
            Column::make('registration_code')->title(__('events::events.registrationCode')),
            Column::make('date')->title(__('events::events.date')),
            Column::make('status')->title(__('events::events.status')),
            Column::make('checkin_time')->title(__('events::events.checkinTime')),
            Column::make('checkout_time')->title(__('events::events.checkoutTime')),
            Column::computed('total_spend_time')->title(__('events::events.totalSpendTime'))->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CheckedInParticipantsReport_'.date('YmdHis');
    }
}
