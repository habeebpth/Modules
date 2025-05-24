<?php
namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\JournalEntry;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class JournalEntryDataTable extends BaseDataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('journal_number', function ($row) {
                return $row->journal ? $row->journal->journal_number : '';
            })
            ->addColumn('journal_date', function ($row) {
                return $row->journal && $row->journal->date ? $row->journal->date->format(company()->date_format) : '';
            })
            ->addColumn('account_code', function ($row) {
                return $row->account ? $row->account->account_code : '';
            })
            ->addColumn('account_name', function ($row) {
                return $row->account ? $row->account->account_name : '';
            })
            ->editColumn('debit', function ($row) {
                return $row->debit > 0 ? currency_format($row->debit) : '';
            })
            ->editColumn('credit', function ($row) {
                return $row->credit > 0 ? currency_format($row->credit) : '';
            })
            ->editColumn('reference_type', function ($row) {
                return $row->reference_type ? ucfirst(str_replace('_', ' ', $row->reference_type)) : '';
            })
            ->addColumn('journal_status', function ($row) {
                if (!$row->journal) return '';

                $badgeClass = match($row->journal->status) {
                    'draft' => 'badge-warning',
                    'posted' => 'badge-success',
                    'reversed' => 'badge-danger',
                    default => 'badge-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->journal->status) . '</span>';
            })
            ->rawColumns(['journal_status'])
            ->addIndexColumn();
    }

    public function query(JournalEntry $model): QueryBuilder
    {
        $request = $this->request();

        $query = $model->where('company_id', user()->company_id)
            ->with(['journal', 'account']);

        // Account filter
        if ($request->account_id && $request->account_id != 'all') {
            $query->where('account_id', $request->account_id);
        }

        // Date filter
        if ($request->startDate && $request->startDate != null && $request->startDate != 'null') {
            $startDate = companyToDateString($request->startDate);
            $query->whereHas('journal', function($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }

        if ($request->endDate && $request->endDate != null && $request->endDate != 'null') {
            $endDate = companyToDateString($request->endDate);
            $query->whereHas('journal', function($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        // Status filter
        if ($request->status && $request->status != 'all') {
            $query->whereHas('journal', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Search filter
        if ($request->searchText && $request->searchText != '') {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->searchText . '%')
                  ->orWhereHas('journal', function($subQ) use ($request) {
                      $subQ->where('journal_number', 'like', '%' . $request->searchText . '%')
                           ->orWhere('description', 'like', '%' . $request->searchText . '%');
                  })
                  ->orWhereHas('account', function($subQ) use ($request) {
                      $subQ->where('account_name', 'like', '%' . $request->searchText . '%')
                           ->orWhere('account_code', 'like', '%' . $request->searchText . '%');
                  });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('journal-entries-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["journal-entries-table"].buttons().container()
                        .appendTo("#table-actions")
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            ['data' => 'journal_number', 'name' => 'journal.journal_number', 'title' => __('Journal #')],
            ['data' => 'journal_date', 'name' => 'journal.date', 'title' => __('Date')],
            ['data' => 'account_code', 'name' => 'account.account_code', 'title' => __('Account Code')],
            ['data' => 'account_name', 'name' => 'account.account_name', 'title' => __('Account Name')],
            ['data' => 'description', 'name' => 'description', 'title' => __('Description')],
            ['data' => 'debit', 'name' => 'debit', 'title' => __('Debit')],
            ['data' => 'credit', 'name' => 'credit', 'title' => __('Credit')],
            ['data' => 'reference_type', 'name' => 'reference_type', 'title' => __('Reference')],
            ['data' => 'journal_status', 'name' => 'journal.status', 'title' => __('Status')],
        ];
    }

    protected function filename(): string
    {
        return 'JournalEntries_' . date('YmdHis');
    }
}
