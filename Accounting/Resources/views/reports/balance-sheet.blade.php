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
            <h4 class="f-21 f-w-500 text-dark">@lang('Balance Sheet')</h4>
            <p class="f-14 text-dark-grey mb-0">@lang('As of') {{ \Carbon\Carbon::parse($asOfDate)->format(company()->date_format) }}</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-light f-12 mr-2">
                {{ count($balanceSheet['assets']) + count($balanceSheet['liabilities']) + count($balanceSheet['equity']) }} @lang('Accounts')
            </span>
            @if($balanceSheet['total_assets'] == $balanceSheet['total_liabilities_equity'])
                <span class="badge badge-success f-12">@lang('Balanced')</span>
            @else
                <span class="badge badge-danger f-12">@lang('Not Balanced')</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <!-- ASSETS -->
            <x-cards.data :title="__('Assets')" padding="false" otherClasses="h-100 border-0">
                <div class="table-responsive">
                    <table class="table table-hover border-0 mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-top-0 f-13 f-w-500">@lang('Account')</th>
                                <th class="border-top-0 f-13 f-w-500 text-right">@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balanceSheet['assets'] as $asset)
                            <tr>
                                <td class="f-13 text-dark">{{ $asset['account']->account_name }}</td>
                                <td class="text-right f-13 f-w-500 text-dark">{{ currency_format($asset['balance']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-lightest">
                                    <i class="fa fa-info-circle mr-1"></i>@lang('No asset accounts found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th class="f-14 f-w-500 text-dark">@lang('Total Assets')</th>
                                <th class="text-right f-14 f-w-500 text-success">{{ currency_format($balanceSheet['total_assets']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>
        </div>

        <div class="col-lg-6 col-md-12">
            <!-- LIABILITIES -->
            <x-cards.data :title="__('Liabilities')" padding="false" otherClasses="border-0 mb-3">
                <div class="table-responsive">
                    <table class="table table-hover border-0 mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-top-0 f-13 f-w-500">@lang('Account')</th>
                                <th class="border-top-0 f-13 f-w-500 text-right">@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balanceSheet['liabilities'] as $liability)
                            <tr>
                                <td class="f-13 text-dark">{{ $liability['account']->account_name }}</td>
                                <td class="text-right f-13 f-w-500 text-dark">{{ currency_format($liability['balance']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-3 text-lightest">
                                    <i class="fa fa-info-circle mr-1"></i>@lang('No liability accounts found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th class="f-14 f-w-500 text-dark">@lang('Total Liabilities')</th>
                                <th class="text-right f-14 f-w-500 text-danger">{{ currency_format($balanceSheet['total_liabilities']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>

            <!-- EQUITY -->
            <x-cards.data :title="__('Equity')" padding="false" otherClasses="border-0">
                <div class="table-responsive">
                    <table class="table table-hover border-0 mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-top-0 f-13 f-w-500">@lang('Account')</th>
                                <th class="border-top-0 f-13 f-w-500 text-right">@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balanceSheet['equity'] as $equity)
                            <tr>
                                <td class="f-13 text-dark">{{ $equity['account']->account_name }}</td>
                                <td class="text-right f-13 f-w-500 text-dark">{{ currency_format($equity['balance']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-3 text-lightest">
                                    <i class="fa fa-info-circle mr-1"></i>@lang('No equity accounts found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th class="f-14 f-w-500 text-dark">@lang('Total Equity')</th>
                                <th class="text-right f-14 f-w-500 text-primary">{{ currency_format($balanceSheet['total_equity']) }}</th>
                            </tr>
                            <tr class="bg-primary text-white">
                                <th class="f-14 f-w-500">@lang('Total Liabilities + Equity')</th>
                                <th class="text-right f-14 f-w-500">{{ currency_format($balanceSheet['total_liabilities_equity']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>
        </div>
    </div>

    <!-- BALANCE CHECK -->
    <div class="row mt-3">
        <div class="col-md-12">
            @if($balanceSheet['total_assets'] == $balanceSheet['total_liabilities_equity'])
            <div class="alert alert-success d-flex align-items-center">
                <i class="fa fa-check-circle mr-2"></i>
                <div>
                    <strong>@lang('Balance Sheet is balanced!')</strong>
                    <p class="mb-0 f-13">@lang('Total Assets equal Total Liabilities + Equity')</p>
                </div>
            </div>
            @else
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fa fa-exclamation-triangle mr-2"></i>
                <div>
                    <strong>@lang('Balance Sheet is not balanced!')</strong>
                    <p class="mb-0 f-13">
                        @lang('Difference'): {{ currency_format(abs($balanceSheet['total_assets'] - $balanceSheet['total_liabilities_equity'])) }}
                    </p>
                </div>
            </div>
            @endif
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
        const url = "{{ route('accounting.reports.balance-sheet') }}";

        if (asOfDate) {
            window.location.href = url + '?as_of_date=' + asOfDate;
        }
    });

    // Export functions
    window.exportReport = function(format) {
        const asOfDate = $('#as_of_date').val();
        const url = "{{ route('accounting.reports.balance-sheet') }}";
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

@media (max-width: 991px) {
    .col-lg-6:first-child {
        margin-bottom: 1rem;
    }
}
</style>
@endpush
