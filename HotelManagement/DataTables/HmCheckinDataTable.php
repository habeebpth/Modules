<?php

namespace Modules\HotelManagement\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\HotelManagement\Entities\HmCheckin;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\HMGuests;

class HmCheckinDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item openRightModal" href="' . route('hm-checkin.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row-checkin" href="javascript:;" data-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            // ->editColumn('check_in', fn($row) => $row->check_in?->translatedFormat($this->company->date_format))
            // ->editColumn('check_out', fn($row) => $row->check_out?->translatedFormat($this->company->date_format))
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn ($row) => 'row-' . $row->id)
            ->rawColumns([ 'action','check']);
    }

    public function query(HmCheckin $model): QueryBuilder
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
        // $leadID = $request->leadID;
        if ($startDate != null && $endDate != null) {
            $model = $model->whereBetween('hm_checkins.created_at', [$startDate, $endDate]);
        }
        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('hm_checkins.check_in', 'like', '%' . request('searchText') . '%')
                        ->orWhere('hm_checkins.check_out', 'like', '%' . request('searchText') . '%')
                        ->orWhere('hm_checkins.arrival_from', 'like', '%' . request('searchText') . '%')
                        ->orWhere('hm_checkins.booking_type_id', 'like', '%' . request('searchText') . '%')
                        ->orWhere('hm_checkins.booking_reference_id', 'like', '%' . request('searchText') . '%')
                        ->orWhere('hm_checkins.purpose_of_visit', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // if ($leadID != 0 && $leadID != null && $leadID != 'all') {
        //     $model = $model->where('contact_person_id', '=', $leadID);
        // }
        $model =  $model->select(
            'booking_types.name as booking_type',
            'hm_booking_sources.name as booking_reference',
            'hm_checkins.id',
            'check_in',
            'check_out',
            'arrival_from',
            'purpose_of_visit',
        )
         ->leftJoin('booking_types', 'booking_types.id', 'hm_checkins.booking_type_id')
         ->leftJoin('hm_booking_sources', 'hm_booking_sources.id', 'hm_checkins.booking_reference_id');
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('hm-checkin-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["hm-checkin-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#hm-checkin-table .select-picker").selectpicker();
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
            'check_in' => ['data' => 'check_in', 'name' => 'check_in', 'title' => 'Check In'],
            'check_out' => ['data' => 'check_out', 'name' => 'check_out', 'title' => 'Check Out'],
            'arrival_from' => ['data' => 'arrival_from', 'name' => 'arrival_from', 'title' => 'Arrival From'],
            'booking_type' => ['data' => 'booking_type', 'name' => 'booking_type', 'title' => 'Booking Type'],
            'booking_reference' => ['data' => 'booking_reference', 'name' => 'booking_reference', 'title' => 'Booking Reference'],
            'purpose_of_visit' => ['data' => 'purpose_of_visit', 'name' => 'purpose_of_visit', 'title' => 'Purpose Of Visit'],



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
        return 'HMcheckin_' . date('YmdHis');
    }

}
