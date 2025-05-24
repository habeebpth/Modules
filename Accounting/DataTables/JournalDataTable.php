<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\Journal;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class JournalDataTable extends BaseDataTable
{
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

                // View action
                $action .= '<a href="' . route('accounting.journals.show', $row->id) . '" class="dropdown-item">
                    <i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                // Edit action (only for draft entries)
                if ($row->status === Journal::STATUS_DRAFT) {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.journals.edit', $row->id) . '">
                        <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';
                }

                // Post action (only for draft entries)
                if ($row->status === Journal::STATUS_DRAFT) {
                    $action .= '<a class="dropdown-item post-journal" href="javascript:;" data-id="' . $row->id . '">
                        <i class="fa fa-check mr-2"></i>' . __('Post Entry') . '</a>';
                }

                // Reverse action (only for posted entries)
                if ($row->status === Journal::STATUS_POSTED) {
                    $action .= '<a class="dropdown-item reverse-journal" href="javascript:;" data-id="' . $row->id . '">
                        <i class="fa fa-undo mr-2"></i>' . __('Reverse Entry') . '</a>';
                }

                $action .= '</div></div></div>';
                return $action;
            })
            ->editColumn('date', function ($row) {
                return $row->date ? $row->date->format(company()->date_format) : '';
            })
            ->editColumn('total_debit', function ($row) {
                return currency_format($row->total_debit);
            })
            ->editColumn('total_credit', function ($row) {
                return currency_format($row->total_credit);
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match($row->status) {
                    Journal::STATUS_DRAFT => 'badge-warning',
                    Journal::STATUS_POSTED => 'badge-success',
                    Journal::STATUS_REVERSED => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('reference_type', function ($row) {
                return $row->reference_type ? ucfirst(str_replace('_', ' ', $row->reference_type)) : '';
            })
            ->addColumn('entries_count', function ($row) {
                return $row->entries_count ?? $row->entries()->count();
            })
            ->rawColumns(['action', 'status', 'check'])
            ->addIndexColumn();
    }

    public function query(Journal $model): QueryBuilder
    {
        $request = $this->request();

        $query = $model->where('company_id', user()->company_id)
            ->with(['entries:journal_id'])
            ->withCount('entries');

        // Date filter
        if ($request->startDate && $request->startDate != null && $request->startDate != 'null') {
            $startDate = companyToDateString($request->startDate);
            $query->where('date', '>=', $startDate);
        }

        if ($request->endDate && $request->endDate != null && $request->endDate != 'null') {
            $endDate = companyToDateString($request->endDate);
            $query->where('date', '<=', $endDate);
        }

        // Status filter
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->searchText && $request->searchText != '') {
            $query->where(function ($q) use ($request) {
                $q->where('journal_number', 'like', '%' . $request->searchText . '%')
                  ->orWhere('description', 'like', '%' . $request->searchText . '%')
                  ->orWhere('reference_type', 'like', '%' . $request->searchText . '%');
            });
        }

        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
    }

    public function html()
    {
        $dataTable = $this->setBuilder('journals-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["journals-table"].buttons().container()
                        .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("#journals-table .select-picker").selectpicker();
                    $(".bs-tooltip-top").removeClass("show");
                }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
        }

        return $dataTable;
    }

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
            ['data' => 'journal_number', 'name' => 'journal_number', 'title' => __('Journal Number')],
            ['data' => 'date', 'name' => 'date', 'title' => __('Date')],
            ['data' => 'description', 'name' => 'description', 'title' => __('Description')],
            ['data' => 'reference_type', 'name' => 'reference_type', 'title' => __('Reference Type')],
            ['data' => 'total_debit', 'name' => 'total_debit', 'title' => __('Total Debit')],
            ['data' => 'total_credit', 'name' => 'total_credit', 'title' => __('Total Credit')],
            ['data' => 'entries_count', 'name' => 'entries_count', 'title' => __('Entries'), 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status')],
            Column::computed('action', __('Action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    protected function filename(): string
    {
        return 'Journals_' . date('YmdHis');
    }
}
