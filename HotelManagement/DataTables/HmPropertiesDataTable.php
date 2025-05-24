<?php

namespace Modules\HotelManagement\DataTables;

use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\HotelManagement\Entities\Property;

class HmPropertiesDataTable extends BaseDataTable
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
                $action .= '<a href="' . route('hm-properties.show', [$row->id]) . '" class="dropdown-item"><i class="mr-2 fa fa-eye"></i>' . __('app.view') . '</a>';

                $action .= '<a class="dropdown-item openRightModal" href="' . route('hm-properties.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                $action .= '<a class="dropdown-item delete-table-row-properties" href="javascript:;" data-id="' . $row->id . '">
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

    public function query(Property $model): QueryBuilder
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
            $model = $model->whereBetween('properties.created_at', [$startDate, $endDate]);
        }
        if ($request->searchText != '') {
            $model = $model->where(
                function ($query) {
                    $query->where('properties.property_name', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.address', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.city', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.state', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.contact_number', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.email', 'like', '%' . request('searchText') . '%')
                        ->orWhere('properties.location', 'like', '%' . request('searchText') . '%');
                }
            );
        }

        // if ($leadID != 0 && $leadID != null && $leadID != 'all') {
        //     $model = $model->where('contact_person_id', '=', $leadID);
        // }
        //    $model =  $model->select(
        //         'leads.client_name as client_name',
        //         'lead_schedules.id',
        //         'lead_schedules.contact_person_id',
        //         'leads.company_name',
        //         'date',
        //         'status',
        //         'meeting_mode',
        //         'time',
        //         'remarks',
        //         'meeting_level',
        //         'meeting_link',
        //         'location',
        //         'date',
        //     )
        //     ->leftJoin('leads', 'leads.id', 'lead_schedules.contact_person_id');
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */


    public function html()
    {
        $dataTable = $this->setBuilder('hm-properties-table')
            ->parameters(
                [
                    'initComplete' => 'function () {
                   window.LaravelDataTables["hm-properties-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                    'fnDrawCallback' => 'function( oSettings ) {
                    $("#hm-properties-table .select-picker").selectpicker();
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
            'property_name' => ['data' => 'property_name', 'name' => 'property_name', 'title' => 'Property Name'],
            'address' => ['data' => 'address', 'name' => 'address', 'title' => 'Address'],
            'city' => ['data' => 'city', 'name' => 'city', 'title' => 'City'],
            'state' => ['data' => 'state', 'name' => 'state', 'title' => 'State'],
            'contact_number' => ['data' => 'contact_number', 'name' => 'contact_number', 'title' => 'Contact Number'],
            'email' => ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            'location' => ['data' => 'location', 'name' => 'location', 'title' => 'Location'],
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
