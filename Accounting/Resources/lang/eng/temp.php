<?php
// =======================
// FILE: Accounting/Resources/views/budgets/create.blade.php
// =======================
?>


<?php
// =======================
// FILE: Accounting/Resources/views/fiscal-years/index.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.button-primary class="mr-3 openRightModal float-left" icon="plus"
                data-toggle="modal" data-target="#createFiscalYearModal">
                @lang('Create Fiscal Year')
            </x-forms.button-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover border-0 w-100">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Start Date')</th>
                        <th>@lang('End Date')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiscalYears as $year)
                    <tr>
                        <td>{{ $year->name }}</td>
                        <td>{{ $year->start_date->format(company()->date_format) }}</td>
                        <td>{{ $year->end_date->format(company()->date_format) }}</td>
                        <td>
                            @if($year->is_active)
                                <span class="badge badge-success">@lang('Active')</span>
                            @else
                                <span class="badge badge-secondary">@lang('Inactive')</span>
                            @endif
                            @if($year->is_closed)
                                <span class="badge badge-danger ml-1">@lang('Closed')</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    @lang('Actions')
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">@lang('Edit')</a>
                                    @if(!$year->is_closed)
                                        <a class="dropdown-item text-danger" href="#">@lang('Close Year')</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">@lang('No fiscal years found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Fiscal Year Modal -->
<div class="modal fade" id="createFiscalYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Create Fiscal Year')</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <x-form id="fiscal-year-form">
                <div class="modal-body">
                    <x-forms.text fieldId="name" :fieldLabel="__('Name')" fieldName="name" fieldRequired="true" />
                    <x-forms.datepicker fieldId="start_date" :fieldLabel="__('Start Date')" fieldName="start_date" fieldRequired="true" />
                    <x-forms.datepicker fieldId="end_date" :fieldLabel="__('End Date')" fieldName="end_date" fieldRequired="true" />
                </div>
                <div class="modal-footer">
                    <x-forms.button-primary id="save-fiscal-year">@lang('app.save')</x-forms.button-primary>
                    <x-forms.button-cancel data-dismiss="modal">@lang('app.cancel')</x-forms.button-cancel>
                </div>
            </x-form>
        </div>
    </div>
</div>

<script>
$('#save-fiscal-year').click(function() {
    const url = "{{ route('accounting.fiscal-years.store') }}";

    $.easyAjax({
        url: url,
        container: '#fiscal-year-form',
        type: "POST",
        data: $('#fiscal-year-form').serialize(),
        success: function(response) {
            if (response.status == 'success') {
                $('#createFiscalYearModal').modal('hide');
                window.location.reload();
            }
        }
    });
});
</script>
@endsection

<?php
// =======================
// FILE: Accounting/Resources/views/tax-codes/index.blade.php
// =======================
?>
@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.tax-codes.create')"
                class="mr-3 openRightModal float-left" icon="plus">
                @lang('Create Tax Code')
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
@endsection

@push('scripts')
@include('sections.datatable_js')

<script>
$('body').on('click', '.delete-tax-code', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('messages.recoverRecord')",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "@lang('messages.confirmDelete')",
        cancelButtonText: "@lang('app.cancel')",
        customClass: {
            confirmButton: 'btn btn-primary mr-3',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            var url = "{{ route('accounting.tax-codes.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'DELETE',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        window.LaravelDataTables["tax-codes-table"].draw(false);
                    }
                }
            });
        }
    });
});
</script>
@endpush

<?php
// =======================
// FILE: Accounting/Resources/views/tax-codes/create.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8">
            <x-cards.data :title="__('Create Tax Code')">
                <x-form id="tax-code-form">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.text fieldId="code" :fieldLabel="__('Tax Code')"
                                fieldName="code" fieldRequired="true" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.text fieldId="name" :fieldLabel="__('Tax Name')"
                                fieldName="name" fieldRequired="true" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.select fieldId="type" :fieldLabel="__('Tax Type')"
                                fieldName="type" fieldRequired="true">
                                <option value="">@lang('Select Type')</option>
                                <option value="sales">@lang('Sales Tax')</option>
                                <option value="purchase">@lang('Purchase Tax')</option>
                                <option value="both">@lang('Both')</option>
                            </x-forms.select>
                        </div>
                        <div class="col-md-6">
                            <x-forms.number fieldId="rate" :fieldLabel="__('Tax Rate (%)')"
                                fieldName="rate" fieldRequired="true" step="0.01" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.select fieldId="tax_account_id" :fieldLabel="__('Tax Account')"
                                fieldName="tax_account_id">
                                <option value="">@lang('Select Tax Account')</option>
                                @foreach($taxAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea fieldId="description" :fieldLabel="__('Description')"
                                fieldName="description" />
                        </div>
                    </div>

                    <x-form-actions>
                        <x-forms.button-primary id="save-tax-code" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('accounting.tax-codes.index')" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </x-form-actions>
                </x-form>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#save-tax-code').click(function() {
    const url = "{{ route('accounting.tax-codes.store') }}";

    $.easyAjax({
        url: url,
        container: '#tax-code-form',
        type: "POST",
        data: $('#tax-code-form').serialize(),
        success: function(response) {
            if (response.status == 'success') {
                window.location.href = response.redirectUrl;
            }
        }
    });
});
</script>
@endsection

<?php
// =======================
// FILE: Accounting/Resources/views/reconciliations/index.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.reconciliations.create')"
                class="mr-3 float-left" icon="plus">
                @lang('New Reconciliation')
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover border-0 w-100">
                <thead>
                    <tr>
                        <th>@lang('Account')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Statement Balance')</th>
                        <th>@lang('Book Balance')</th>
                        <th>@lang('Difference')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $reconciliation)
                    <tr>
                        <td>{{ $reconciliation->account->account_name }}</td>
                        <td>{{ $reconciliation->reconciliation_date->format(company()->date_format) }}</td>
                        <td>{{ currency_format($reconciliation->statement_balance) }}</td>
                        <td>{{ currency_format($reconciliation->book_balance) }}</td>
                        <td class="{{ $reconciliation->difference == 0 ? 'text-success' : 'text-danger' }}">
                            {{ currency_format($reconciliation->difference) }}
                        </td>
                        <td>
                            @php
                                $badgeClass = match($reconciliation->status) {
                                    'draft' => 'badge-warning',
                                    'completed' => 'badge-success',
                                    'reviewed' => 'badge-primary',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($reconciliation->status) }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    @lang('Actions')
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('accounting.reconciliations.show', $reconciliation->id) }}">@lang('View')</a>
                                    @if($reconciliation->status === 'draft')
                                        <a class="dropdown-item" href="{{ route('accounting.reconciliations.edit', $reconciliation->id) }}">@lang('Edit')</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">@lang('No reconciliations found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<?php
// =======================
// FILE: Accounting/Resources/views/reconciliations/create.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8">
            <x-cards.data :title="__('Create Bank Reconciliation')">
                <x-form id="reconciliation-form">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.select fieldId="account_id" :fieldLabel="__('Bank Account')"
                                fieldName="account_id" fieldRequired="true">
                                <option value="">@lang('Select Bank Account')</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="col-md-6">
                            <x-forms.datepicker fieldId="reconciliation_date" :fieldLabel="__('Reconciliation Date')"
                                fieldName="reconciliation_date" fieldRequired="true" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.number fieldId="statement_balance" :fieldLabel="__('Statement Balance')"
                                fieldName="statement_balance" fieldRequired="true" step="0.01" />
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Book Balance')</label>
                                <input type="text" class="form-control" id="book_balance" readonly placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea fieldId="notes" :fieldLabel="__('Notes')"
                                fieldName="notes" />
                        </div>
                    </div>

                    <x-form-actions>
                        <x-forms.button-primary id="save-reconciliation" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('accounting.reconciliations.index')" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </x-form-actions>
                </x-form>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#account_id').on('change', function() {
    const accountId = $(this).val();
    const date = $('#reconciliation_date').val();

    if (accountId && date) {
        // Fetch book balance for selected account and date
        $.get("{{ route('api.accounting.book-balance') }}", {
            account_id: accountId,
            date: date
        }, function(data) {
            $('#book_balance').val(data.balance);
        });
    }
});

$('#save-reconciliation').click(function() {
    const url = "{{ route('accounting.reconciliations.store') }}";

    $.easyAjax({
        url: url,
        container: '#reconciliation-form',
        type: "POST",
        data: $('#reconciliation-form').serialize(),
        success: function(response) {
            if (response.status == 'success') {
                window.location.href = "{{ route('accounting.reconciliations.index') }}";
            }
        }
    });
});
</script>
@endsection

<?php
// =======================
// FILE: Accounting/Resources/views/closing-entries/index.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-md-12">
            <x-cards.data :title="__('Year-End Closing')">
                <div class="row">
                    <div class="col-md-6">
                        <x-forms.select fieldId="fiscal_year_id" :fieldLabel="__('Select Fiscal Year to Close')"
                            fieldName="fiscal_year_id">
                            <option value="">@lang('Select Fiscal Year')</option>
                            @foreach($fiscalYears as $year)
                                <option value="{{ $year->id }}" {{ $year->is_closed ? 'disabled' : '' }}>
                                    {{ $year->name }} {{ $year->is_closed ? '(Closed)' : '' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <x-forms.button-primary id="close-year" icon="lock">
                            @lang('Close Fiscal Year')
                        </x-forms.button-primary>
                    </div>
                </div>
            </x-cards.data>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-cards.data :title="__('Closing Entries History')">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>@lang('Fiscal Year')</th>
                                <th>@lang('Journal #')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($closingEntries as $entry)
                            <tr>
                                <td>{{ $entry->fiscalYear->name }}</td>
                                <td>{{ $entry->journal->journal_number }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($entry->type) {
                                            'revenue' => 'badge-success',
                                            'expense' => 'badge-danger',
                                            'dividend' => 'badge-warning',
                                            'summary' => 'badge-info',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($entry->type) }}</span>
                                </td>
                                <td>{{ $entry->closing_date->format(company()->date_format) }}</td>
                                <td>{{ currency_format($entry->amount) }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>
                                    <a href="{{ route('accounting.journals.show', $entry->journal_id) }}" class="btn btn-sm btn-outline-primary">
                                        @lang('View Journal')
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">@lang('No closing entries found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#close-year').click(function() {
    const fiscalYearId = $('#fiscal_year_id').val();

    if (!fiscalYearId) {
        Swal.fire({
            title: '@lang("Select Fiscal Year")',
            text: '@lang("Please select a fiscal year to close")',
            icon: 'warning'
        });
        return;
    }

    Swal.fire({
        title: '@lang("Close Fiscal Year?")',
        text: '@lang("This action cannot be undone. All revenue and expense accounts will be closed to retained earnings.")',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '@lang("Yes, Close Year")',
        cancelButtonText: '@lang("Cancel")'
    }).then((result) => {
        if (result.isConfirmed) {
            $.easyAjax({
                url: "{{ route('accounting.closing-entries.close') }}",
                type: "POST",
                data: {
                    fiscal_year_id: fiscalYearId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endsection

<?php
// =======================
// FILE: Accounting/Resources/views/import-export/index.blade.php
// =======================
?>
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-6">
            <x-cards.data :title="__('Import Chart of Accounts')">
                <x-form id="import-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>@lang('Select File')</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx" required>
                        <small class="text-muted">@lang('Supported formats: CSV, Excel')</small>
                    </div>

                    <x-forms.button-primary id="import-accounts" icon="upload">
                        @lang('Import Accounts')
                    </x-forms.button-primary>
                </x-form>

                <hr>

                <div class="text-center">
                    <a href="#" class="btn btn-outline-secondary" id="download-template">
                        <i class="fa fa-download mr-1"></i>@lang('Download Template')
                    </a>
                </div>
            </x-cards.data>
        </div>

        <div class="col-md-6">
            <x-cards.data :title="__('Export Chart of Accounts')">
                <p>@lang('Export your complete chart of accounts to Excel format.')</p>

                <a href="{{ route('accounting.export.chart-of-accounts') }}" class="btn btn-success">
                    <i class="fa fa-download mr-1"></i>@lang('Export to Excel')
                </a>
            </x-cards.data>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <x-cards.data :title="__('Import Results')" id="import-results" style="display: none;">
                <div id="results-content"></div>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#import-accounts').click(function(e) {
    e.preventDefault();

    const formData = new FormData($('#import-form')[0]);

    $.ajax({
        url: "{{ route('accounting.import.chart-of-accounts') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                $('#import-results').show();
                let html = `
                    <div class="alert alert-success">
                        <strong>Import Successful!</strong><br>
                        ${response.data.imported} accounts imported successfully.
                    </div>
                `;

                if (response.data.errors.length > 0) {
                    html += `
                        <div class="alert alert-warning">
                            <strong>Errors:</strong>
                            <ul class="mb-0">
                    `;
                    response.data.errors.forEach(error => {
                        html += `<li>${error}</li>`;
                    });
                    html += '</ul></div>';
                }

                $('#results-content').html(html);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire({
                title: 'Import Failed',
                text: response.message || 'An error occurred during import',
                icon: 'error'
            });
        }
    });
});
</script>
@endsection

<?php
// =======================
// FILE: Accounting/DataTables/FiscalYearDataTable.php
// =======================

namespace Modules\Accounting\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Accounting\Entities\FiscalYear;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class FiscalYearDataTable extends BaseDataTable
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

                $action .= '<a class="dropdown-item openRightModal" href="' . route('accounting.fiscal-years.edit', $row->id) . '">
                    <i class="fa fa-edit mr-2"></i>' . __('app.edit') . '</a>';

                if (!$row->is_closed) {
                    $action .= '<a class="dropdown-item close-fiscal-year" href="javascript:;" data-id="' . $row->id . '">
                        <i class="fa fa-lock mr-2"></i>' . __('Close Year') . '</a>';
                }

                $action .= '</div></div></div>';
                return $action;
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date ? $row->start_date->format(company()->date_format) : '';
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date ? $row->end_date->format(company()->date_format) : '';
            })
            ->editColumn('is_active', function ($row) {
                $status = $row->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                if ($row->is_closed) {
                    $status .= ' <span class="badge badge-danger ml-1">Closed</span>';
                }
                return $status;
            })
            ->rawColumns(['action', 'is_active', 'check'])
            ->addIndexColumn();
    }

    public function query(FiscalYear $model): QueryBuilder
    {
        return $model->where('company_id', user()->company_id)
            ->orderBy('start_date', 'desc');
    }

    public function html()
    {
        return $this->setBuilder('fiscal-years-table');
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
            ['data' => 'name', 'name' => 'name', 'title' => __('Name')],
            ['data' => 'start_date', 'name' => 'start_date', 'title' => __('Start Date')],
            ['data' => 'end_date', 'name' => 'end_date', 'title' => __('End Date')],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => __('Status')],
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
        return 'FiscalYears_' . date('YmdHis');
    }
}

?><?php
// =======================
// FILE: Accounting/Listeners/UpdateBudgetActuals.php
// =======================

namespace Modules\Accounting\Listeners;

use Modules\Accounting\Events\JournalPosted;
use Modules\Accounting\Services\BudgetService;
use Modules\Accounting\Entities\Budget;

class UpdateBudgetActuals
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function handle(JournalPosted $event)
    {
        // Update budget actual amounts when journal is posted
        foreach ($event->journal->entries as $entry) {
            $budgets = Budget::where('account_id', $entry->account_id)->get();

            foreach ($budgets as $budget) {
                $this->budgetService->updateActualAmount($budget->id);
            }
        }
    }
}

?><?php
// =======================
// FILE: Accounting/Http/Requests/JournalEntryRequest.php
// =======================

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounting\Rules\BalancedJournalRule;

class JournalEntryRequest extends FormRequest
{
    public function authorize()
    {
        return in_array('accounting', user()->modules ?? []);
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'entries' => ['required', 'array', 'min:2', new BalancedJournalRule],
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'entries.min' => 'Journal entry must have at least 2 entries',
            'entries.*.account_id.required' => 'Account is required for each entry',
            'entries.*.account_id.exists' => 'Selected account does not exist',
        ];
    }
}

?><?php
// =======================
// FILE: Accounting/Http/Requests/ChartOfAccountRequest.php
// =======================

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounting\Entities\ChartOfAccount;

class ChartOfAccountRequest extends FormRequest
{
    public function authorize()
    {
        return in_array('accounting', user()->modules ?? []);
    }

    public function rules()
    {
        $accountId = $this->route('chart_of_account');

        return [
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code,' . $accountId,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', array_keys(ChartOfAccount::ACCOUNT_TYPES)),
            'account_sub_type' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'account_code.unique' => 'Account code already exists',
            'account_type.in' => 'Invalid account type selected',
            'parent_id.exists' => 'Selected parent account does not exist',
        ];
    }
}

?><?php
// =======================
// FILE: Accounting/Console/Commands/RecalculateAccountBalances.php
// =======================

namespace Modules\Accounting\Console\Commands;

use Illuminate\Console\Command;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\Accounting\Services\AccountingService;

class RecalculateAccountBalances extends Command
{
    protected $signature = 'accounting:recalculate-balances {--company=}';
    protected $description = 'Recalculate all account balances from journal entries';

    public function handle()
    {
        $companyId = $this->option('company');
        $accountingService = app(AccountingService::class);

        $query = ChartOfAccount::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
            $this->info("Recalculating balances for company ID: {$companyId}");
        } else {
            $this->info("Recalculating balances for all companies");
        }

        $accounts = $query->get();
        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();

        $recalculated = 0;
        foreach ($accounts as $account) {
            $oldBalance = $account->current_balance;
            $accountingService->updateAccountBalance($account->id);
            $account->refresh();

            if ($oldBalance != $account->current_balance) {
                $recalculated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Recalculation complete! {$recalculated} account balances were updated.");
    }
}

?><?php
// =======================
// FILE: Accounting/Notifications/JournalPostedNotification.php
// =======================

namespace Modules\Accounting\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Accounting\Entities\Journal;

class JournalPostedNotification extends Notification
{
    protected $journal;

    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Journal Entry Posted')
            ->line('A journal entry has been posted to the accounting system.')
            ->line('Journal Number: ' . $this->journal->journal_number)
            ->line('Amount: ' . currency_format($this->journal->total_debit))
            ->line('Description: ' . $this->journal->description)
            ->action('View Journal Entry', route('accounting.journals.show', $this->journal->id));
    }

    public function toArray($notifiable)
    {
        return [
            'journal_id' => $this->journal->id,
            'journal_number' => $this->journal->journal_number,
            'amount' => $this->journal->total_debit,
            'description' => $this->journal->description,
        ];
    }
}
