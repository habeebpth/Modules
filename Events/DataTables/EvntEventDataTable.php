<?php

namespace Modules\Events\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\Models\GlobalSetting;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Events\Entities\EvntEvent;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\Property;

class EvntEventDataTable extends BaseDataTable
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
                $action .= '<a href="' . route('events.show', [$row->id]) . '" class="dropdown-item"><i class="mr-2 fa fa-eye"></i>' . __('app.view') . '</a>';
                $action .= '<a class="dropdown-item" href="' . url()->temporarySignedRoute('front.event-publicurl.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), [$row->slug]) . '" target="_blank"><i class="fa fa-link mr-2"></i>' . __('modules.proposal.publicLink') . '</a>';
                $action .= '<a class="dropdown-item" href="' . url()->temporarySignedRoute('front.event-publicurl-new.show', now()->addDays(GlobalSetting::SIGNED_ROUTE_EXPIRY), [$row->slug]) . '" target="_blank"><i class="fa fa-link mr-2"></i>' . __('app.publicLinkNew') . '</a>';
                $action .= '<a class="dropdown-item openRightModal" href="' . route('events.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row-events" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('end_date_time', function ($row) {
                return \Carbon\Carbon::parse($row->end_date_time)->format('d-m-Y H:i');
            })
            ->editColumn('start_date_time', function ($row) {
                return \Carbon\Carbon::parse($row->start_date_time)->format('d-m-Y H:i');
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns([ 'action','check']);
    }

    public function query(EvntEvent $model): QueryBuilder
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

        // Ensure $model is a query builder instance
        $query = $model->newQuery();

        if ($startDate && $endDate) {
            $query->whereBetween('evnt_events.created_at', [$startDate, $endDate]);
        }

        $searchText = $request->searchText ?? '';
        if (!empty($searchText)) {
            $query->where(function ($q) use ($searchText) {
                $q->where('evnt_events.property_name', 'like', "%$searchText%")
                  ->orWhere('evnt_events.address', 'like', "%$searchText%");
            });
        }

        return $query; // Ensuring return type is QueryBuilder
    }


    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('evnt-event-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["evnt-event-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#evnt-event-table .select-picker").selectpicker();
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
            'name' => ['data' => 'name', 'name' => 'name', 'title' => 'Event Name'],
            'start_date_time' => ['data' => 'start_date_time', 'name' => 'start_date_time', 'title' => 'Start Date Time'],
            'end_date_time' => ['data' => 'end_date_time', 'name' => 'end_date_time', 'title' => 'End Date Time'],
            'status' => ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
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
        return 'HMProperties_' . date('YmdHis');
    }

}
