<?php

namespace Modules\Synktime\DataTables;

use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;
use Modules\Synktime\Entities\SynkingHistory;

class SynkingHistoryDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item" href="' . route('synking-history.show', [$row->id]) . '">
                                <i class="fa fa-eye mr-2"></i>
                                ' . trans('app.view') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->addColumn('sync_type_formatted', function ($row) {
                $syncType = $row->sync_type ?? 'attendance'; // Default to attendance for backward compatibility

                switch ($syncType) {
                    case 'department':
                        return '<span class="badge badge-primary">' . __('synktime::app.department_sync') . '</span>';
                    case 'area':
                        return '<span class="badge badge-success">' . __('synktime::app.area_sync') . '</span>';
                    case 'employee':
                        return '<span class="badge badge-info">' . __('synktime::app.employee_sync') . '</span>';
                    default:
                        return '<span class="badge badge-secondary">' . __('synktime::app.attendance_sync') . '</span>';
                }
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check', 'sync_type_formatted']);
    }

    public function query(SynkingHistory $model): QueryBuilder
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

        $model = $model->where('synking_history.company_id', company()->id);

        // Filter only entity syncs if requested
        if ($request->has('show_entity_syncs') && $request->show_entity_syncs === 'true') {
            $model = $model->whereIn('sync_type', ['department', 'area', 'employee']);
        }
        // Show only attendance syncs if specifically requested
        elseif ($request->has('show_attendance_syncs') && $request->show_attendance_syncs === 'true') {
            $model = $model->where(function($query) {
                $query->where('sync_type', 'attendance')
                      ->orWhereNull('sync_type'); // For backward compatibility
            });
        }

        if ($startDate != null && $endDate != null) {
            $model = $model->whereBetween('synking_history.created_at', [$startDate, $endDate]);
        }

        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('creator.name', 'like', '%' . request('searchText') . '%')
                        ->orWhere('synking_history.from_date', 'like', '%' . request('searchText') . '%')
                        ->orWhere('synking_history.to_date', 'like', '%' . request('searchText') . '%')
                        ->orWhere('synking_history.sync_type', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // Build the query with appropriate joins
        $query = $model->select([
            'creator.name as created',
            'employee.name as employee',
            'synking_history.id',
            'synking_history.created_by',
            'synking_history.from_date',
            'synking_history.to_date',
            'synking_history.sync_type',
            'synking_history.total_synced',
            \DB::raw('CONVERT_TZ(synking_history.created_at, "+00:00", "+05:30") as created_at_ist'), // Convert to IST
            \DB::raw('DATE_FORMAT(CONVERT_TZ(synking_history.created_at, "+00:00", "+05:30"), "%h:%i %p") as created_time') // Format in IST
        ])
        ->leftJoin('users as creator', 'creator.id', '=', 'synking_history.created_by')
        ->leftJoin('users as employee', 'employee.id', '=', 'synking_history.employee_id');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html()
    {
        $dataTable = $this->setBuilder('synking-history-table')
            ->parameters(
                [
                    'scrollX' => true,
                    'initComplete' => 'function () {
                   window.LaravelDataTables["synking-history-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#synking-history-table .select-picker").selectpicker();
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
        // Determine if we're showing entity syncs or attendance syncs
        $request = $this->request();
        $showEntitySyncs = $request->has('show_entity_syncs') && $request->show_entity_syncs === 'true';

        $columns = [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            'created_by' => ['data' => 'created', 'name' => 'creator.name', 'title' => __('app.createdBy')],
            'sync_type' => [
                'data' => 'sync_type_formatted',
                'name' => 'synking_history.sync_type',
                'title' => __('synktime::app.sync_type'),
                'orderable' => true,
                'searchable' => true,
            ]
        ];

        // Add columns specific to entity sync or attendance sync
        if ($showEntitySyncs) {
            $columns['total_synced'] = [
                'data' => 'total_synced',
                'name' => 'synking_history.total_synced',
                'title' => __('synktime::app.total_synced'),
                'orderable' => true,
                'searchable' => true,
            ];
        } else {
            $columns['employee'] = [
                'data' => 'employee',
                'name' => 'employee.name',
                'title' => __('app.employee'),
            ];
            $columns['from_date'] = [
                'data' => 'from_date',
                'name' => 'synking_history.from_date',
                'title' => __('synktime::app.from_date'),
            ];
            $columns['to_date'] = [
                'data' => 'to_date',
                'name' => 'synking_history.to_date',
                'title' => __('synktime::app.to_date'),
            ];
        }

        $columns['created_time'] = [
            'data' => 'created_time',
            'name' => 'synking_history.created_at',
            'title' => __('app.createdAt') . ' (IST)',
        ];

        $columns['action'] = [
            'data' => 'action',
            'name' => 'action',
            'title' => __('app.action'),
            'exportable' => false,
            'orderable' => false,
            'searchable' => false
        ];

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'synking_history_' . date('YmdHis');
    }
}
