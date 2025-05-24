<?php

namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Illuminate\Database\Eloquent\Builder;
use Modules\Accounting\Entities\JournalEntry;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class JournalEntriesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link" id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('journal-entries.show', $row->id) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($row->status == 'draft') {
                    $action .= '<a href="' . route('journal-entries.edit', $row->id) . '" class="dropdown-item openRightModal"><i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';
                }

                if ($row->status == 'draft') {
                    $action .= '<a href="javascript:;" data-journal-entry-id="' . $row->id . '" class="dropdown-item post-journal-entry"><i class="fa fa-check mr-2"></i>' . __('app.post') . '</a>';
                }

                if ($row->status == 'posted') {
                    $action .= '<a href="javascript:;" data-journal-entry-id="' . $row->id . '" class="dropdown-item void-journal-entry"><i class="fa fa-ban mr-2"></i>' . __('app.void') . '</a>';
                }

                $action .= '<a href="javascript:;" data-journal-entry-id="' . $row->id . '" class="dropdown-item delete-journal-entry"><i class="fa fa-trash mr-2"></i>' . __('app.delete') . '</a>';

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('reference_number', function ($row) {
                return '<a href="' . route('journal-entries.show', $row->id) . '">' . $row->reference_number . '</a>';
            })
            ->editColumn('entry_date', function ($row) {
                return $row->entry_date->format(company()->date_format);
            })
            ->editColumn('status', function ($row) {
                $statusClass = [
                    'draft' => 'warning',
                    'posted' => 'success',
                    'voided' => 'danger',
                ];

                return '<span class="badge badge-' . $statusClass[$row->status] . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('total_debit', function ($row) {
                return currency_format($row->total_debit, company()->currency);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'reference_number', 'status']);
    }

    /**
     * Get query source of dataTable.
     */
    public function query(JournalEntry $model): Builder
    {
        $request = $this->request();

        $model = $model->select('journal_entries.*')
            ->where('journal_entries.company_id', company()->id);

        if ($request->searchText) {
            $model->where(function ($query) use ($request) {
                $query->where('journal_entries.reference_number', 'like', '%' . $request->searchText . '%')
                    ->orWhere('journal_entries.description', 'like', '%' . $request->searchText . '%');
            });
        }

        if ($request->startDate && $request->endDate) {
            $model->whereBetween('journal_entries.entry_date', [$request->startDate, $request->endDate]);
        }

        if ($request->status && $request->status != 'all') {
            $model->where('journal_entries.status', $request->status);
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('journal-entries-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->dom($this->domHtml)
            ->language(__('app.datatable'))
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["journal-entries-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     */
    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title(__('app.srNo')),
            Column::make('reference_number')->title(__('app.referenceNumber')),
            Column::make('entry_date')->title(__('app.date')),
            Column::make('description')->title(__('app.description')),
            Column::make('status')->title(__('app.status')),
            Column::make('total_debit')->title(__('app.amount')),
            Column::computed('action')->exportable(false)->printable(false)->width(120)->addClass('text-center'),
        ];
    }
}
