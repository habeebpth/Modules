<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\ClosingEntry;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ClosingEntryDataTable extends BaseDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">';
                $action .= '<div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= '<a href="' . route('accounting.journals.show', $row->journal_id) . '" class="dropdown-item">
                    <i class="fa fa-eye mr-2"></i>' . __('View Journal') . '</a>';

                $action .= '</div></div></div>';
                return $action;
            })
            ->addColumn('fiscal_year_name', function ($row) {
                return $row->fiscalYear ? $row->fiscalYear->name : '';
            })
            ->addColumn('journal_number', function ($row) {
                return $row->journal ? $row->journal->journal_number : '';
            })
            ->editColumn('closing_date', function ($row) {
                return $row->closing_date ? $row->closing_date->format(company()->date_format) : '';
            })
            ->editColumn('amount', function ($row) {
                return currency_format($row->amount);
            })
            ->editColumn('type', function ($row) {
                $badgeClass = match($row->type) {
                    'revenue' => 'badge-success',
                    'expense' => 'badge-danger',
                    'dividend' => 'badge-warning',
                    'summary' => 'badge-info',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->type) . '</span>';
            })
            ->rawColumns(['action', 'type'])
            ->addIndexColumn();
    }

    public function query(ClosingEntry $model): QueryBuilder
    {
        return $model->where('company_id', user()->company_id)
            ->with(['fiscalYear', 'journal'])
            ->orderBy('closing_date', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('closing-entries-table');
    }

    public function getColumns(): array
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            ['data' => 'fiscal_year_name', 'name' => 'fiscalYear.name', 'title' => __('Fiscal Year')],
            ['data' => 'journal_number', 'name' => 'journal.journal_number', 'title' => __('Journal #')],
            ['data' => 'type', 'name' => 'type', 'title' => __('Type')],
            ['data' => 'closing_date', 'name' => 'closing_date', 'title' => __('Closing Date')],
            ['data' => 'amount', 'name' => 'amount', 'title' => __('Amount')],
            ['data' => 'description', 'name' => 'description', 'title' => __('Description')],
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
        return 'ClosingEntries_' . date('YmdHis');
    }
}
