@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <select name="account_id" id="account_id" class="form-control select-picker" data-live-search="true">
            <option value="">@lang('Select Account')</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ isset($selectedAccount) && $selectedAccount->id == $account->id ? 'selected' : '' }}>
                    {{ $account->account_code }} - {{ $account->account_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <x-forms.datepicker fieldId="from_date" :fieldLabel="__('From Date')"
            fieldName="from_date" :fieldValue="$fromDate"
            fieldPlaceholder="@lang('Select From Date')" />
    </div>
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <x-forms.datepicker fieldId="to_date" :fieldLabel="__('To Date')"
            fieldName="to_date" :fieldValue="$toDate"
            fieldPlaceholder="@lang('Select To Date')" />
    </div>
    <div class="select-box py-2 px-lg-2 px-md-2 px-0">
        <button type="button" class="btn btn-primary" id="apply-filter">
            @lang('Apply Filter')
        </button>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            @if(isset($selectedAccount))
                <x-cards.data :title="__('General Ledger') . ' - ' . $selectedAccount->account_code . ' ' . $selectedAccount->account_name" padding="false">
                    <!-- Account Summary -->
                    <div class="p-4 border-bottom bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="f-14 text-dark-grey mb-1">@lang('Account Code')</label>
                                    <p class="f-15 text-dark f-w-500">{{ $selectedAccount->account_code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="f-14 text-dark-grey mb-1">@lang('Account Type')</label>
                                    <p class="f-15 text-dark">{{ ucfirst($selectedAccount->account_type) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="f-14 text-dark-grey mb-1">@lang('Opening Balance')</label>
                                    <p class="f-15 text-dark f-w-500">{{ currency_format($ledgerEntries['opening_balance']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger Entries -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Journal #')</th>
                                    <th>@lang('Description')</th>
                                    <th class="text-right">@lang('Debit')</th>
                                    <th class="text-right">@lang('Credit')</th>
                                    <th class="text-right">@lang('Balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                @if($ledgerEntries['opening_balance'] != 0)
                                <tr class="bg-light">
                                    <td>{{ \Carbon\Carbon::parse($fromDate)->subDay()->format(company()->date_format) }}</td>
                                    <td>-</td>
                                    <td><em>@lang('Opening Balance')</em></td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right f-w-500">{{ currency_format($ledgerEntries['opening_balance']) }}</td>
                                </tr>
                                @endif

                                <!-- Transaction Entries -->
                                @forelse($ledgerEntries['entries'] as $entry)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format(company()->date_format) }}</td>
                                    <td>{{ $entry['journal_number'] }}</td>
                                    <td>{{ $entry['description'] }}</td>
                                    <td class="text-right">
                                        {{ $entry['debit'] > 0 ? currency_format($entry['debit']) : '-' }}
                                    </td>
                                    <td class="text-right">
                                        {{ $entry['credit'] > 0 ? currency_format($entry['credit']) : '-' }}
                                    </td>
                                    <td class="text-right f-w-500">{{ currency_format($entry['balance']) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <em>@lang('No transactions found for the selected period')</em>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($ledgerEntries['entries']))
                            <tfoot>
                                <tr class="bg-primary text-white">
                                    <th colspan="5">@lang('Closing Balance')</th>
                                    <th class="text-right">{{ currency_format($ledgerEntries['closing_balance']) }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </x-cards.data>
            @else
                <x-cards.data :title="__('General Ledger')" padding="true">
                    <div class="text-center py-5">
                        <i class="fa fa-book fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">@lang('Select Account')</h4>
                        <p class="text-muted">@lang('Please select an account from the filter above to view its general ledger.')</p>
                    </div>
                </x-cards.data>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#apply-filter').click(function() {
    const accountId = $('#account_id').val();
    const fromDate = $('#from_date').val();
    const toDate = $('#to_date').val();
    const url = "{{ route('accounting.reports.general-ledger') }}";

    if (!accountId) {
        Swal.fire({
            title: "@lang('Select Account')",
            text: "@lang('Please select an account to view its general ledger.')",
            icon: 'warning',
            confirmButtonText: "@lang('app.ok')"
        });
        return;
    }

    let params = 'account_id=' + accountId;
    if (fromDate) params += '&from_date=' + fromDate;
    if (toDate) params += '&to_date=' + toDate;

    window.location.href = url + '?' + params;
});

// Auto-submit when account is selected
$('#account_id').on('change', function() {
    if ($(this).val()) {
        $('#apply-filter').click();
    }
});

$('.select-picker').selectpicker();
</script>
@endpush
