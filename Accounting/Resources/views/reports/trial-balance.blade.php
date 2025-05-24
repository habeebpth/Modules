@extends('layouts.app')

@push('styles')
<style>
.select-picker {
    border: 1px solid #e3e6f0 !important;
}
</style>
@endpush

@section('filter-section')
<x-filters.filter-box>
    <!-- DATE RANGE START -->
    <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('As of Date')</p>
        <div class="select-status d-flex">
            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                id="as_of_date" placeholder="@lang('Select Date')" value="{{ $asOfDate }}">
        </div>
    </div>
    <!-- DATE RANGE END -->

    <!-- APPLY FILTER START -->
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <button type="button" class="btn btn-primary f-14" id="apply-filter">
            <i class="fa fa-filter mr-1"></i>@lang('Apply Filter')
        </button>
    </div>
    <!-- APPLY FILTER END -->

    <!-- EXPORT OPTIONS START -->
    <div class="select-box py-2 px-lg-2 px-md-2 px-0">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle f-14" type="button"
                id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-download mr-1"></i>@lang('Export')
            </button>
            <div class="dropdown-menu" aria-labelledby="exportDropdown">
                <a class="dropdown-item" href="javascript:;" onclick="exportReport('excel')">
                    <i class="fa fa-file-excel mr-2"></i>@lang('Excel')
                </a>
                <a class="dropdown-item" href="javascript:;" onclick="exportReport('pdf')">
                    <i class="fa fa-file-pdf mr-2"></i>@lang('PDF')
                </a>
                <a class="dropdown-item" href="javascript:;" onclick="printReport()">
                    <i class="fa fa-print mr-2"></i>@lang('Print')
                </a>
            </div>
        </div>
    </div>
    <!-- EXPORT OPTIONS END -->
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- REPORT HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="f-21 f-w-500 text-dark">@lang('Trial Balance')</h4>
            <p class="f-14 text-dark-grey mb-0">@lang('As of') {{ \Carbon\Carbon::parse($asOfDate)->format(company()->date_format) }}</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-light f-12">
                {{ count($trialBalance['accounts']) }} @lang('Accounts')
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-cards.data padding="false" otherClasses="border-0">
                <div class="table-responsive">
                    <table class="table table-hover border-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-top-0 f-13 f-w-500">@lang('Account Code')</th>
                                <th class="border-top-0 f-13 f-w-500">@lang('Account Name')</th>
                                <th class="border-top-0 f-13 f-w-500 text-right">@lang('Debit')</th>
                                <th class="border-top-0 f-13 f-w-500 text-right">@lang('Credit')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialBalance['accounts'] as $account)
                            <tr>
                                <td class="f-12 text-dark-grey">{{ $account['account']->account_code }}</td>
                                <td class="f-13 text-dark">{{ $account['account']->account_name }}</td>
                                <td class="text-right f-13">
                                    @if($account['debit_balance'] > 0)
                                        <span class="text-dark f-w-500">{{ currency_format($account['debit_balance']) }}</span>
                                    @else
                                        <span class="text-lightest">-</span>
                                    @endif
                                </td>
                                <td class="text-right f-13">
                                    @if($account['credit_balance'] > 0)
                                        <span class="text-dark f-w-500">{{ currency_format($account['credit_balance']) }}</span>
                                    @else
                                        <span class="text-lightest">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="2" class="f-14 f-w-500 text-dark">@lang('Total')</th>
                                <th class="text-right f-14 f-w-500 text-dark">{{ currency_format($trialBalance['total_debits']) }}</th>
                                <th class="text-right f-14 f-w-500 text-dark">{{ currency_format($trialBalance['total_credits']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($trialBalance['total_debits'] == $trialBalance['total_credits'])
                <div class="px-4 py-3 bg-light-success border-top">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-check-circle text-success mr-2"></i>
                        <span class="f-14 text-success">@lang('Trial Balance is balanced')</span>
                    </div>
                </div>
                @else
                <div class="px-4 py-3 bg-light-danger border-top">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-exclamation-triangle text-danger mr-2"></i>
                        <span class="f-14 text-danger">@lang('Trial Balance is not balanced - Please check journal entries')</span>
                    </div>
                </div>
                @endif
            </x-cards.data>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize datepicker
    $('#as_of_date').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: '{{ company()->moment_date_format }}'
        },
        showDropdowns: true,
        autoUpdateInput: true
    });

    // Apply filter
    $('#apply-filter').click(function() {
        const asOfDate = $('#as_of_date').val();
        const url = "{{ route('accounting.reports.trial-balance') }}";

        if (asOfDate) {
            window.location.href = url + '?as_of_date=' + asOfDate;
        }
    });

    // Export functions
    window.exportReport = function(format) {
        const asOfDate = $('#as_of_date').val();
        const url = "{{ route('accounting.reports.trial-balance') }}";
        const params = new URLSearchParams({
            as_of_date: asOfDate,
            export: format
        });

        window.open(url + '?' + params.toString(), '_blank');
    };

    // Print function
    window.printReport = function() {
        window.print();
    };
});
</script>

<style>
@media print {
    .filter-section, .sidebar, .navbar, .footer, .btn, .dropdown {
        display: none !important;
    }
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endpush
