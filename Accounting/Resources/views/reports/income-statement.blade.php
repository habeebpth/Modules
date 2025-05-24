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
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Period')</p>
        <div class="select-status d-flex">
            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                id="dateRange" placeholder="@lang('Select Date Range')"
                value="{{ \Carbon\Carbon::parse($fromDate)->format(company()->date_format) }} - {{ \Carbon\Carbon::parse($toDate)->format(company()->date_format) }}">
        </div>
    </div>
    <!-- DATE RANGE END -->

    <!-- QUICK PERIODS START -->
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <select class="form-control select-picker f-14" id="quick-period" data-size="5">
            <option value="">@lang('Quick Period')</option>
            <option value="current_month">@lang('Current Month')</option>
            <option value="last_month">@lang('Last Month')</option>
            <option value="current_quarter">@lang('Current Quarter')</option>
            <option value="last_quarter">@lang('Last Quarter')</option>
            <option value="current_year">@lang('Current Year')</option>
            <option value="last_year">@lang('Last Year')</option>
        </select>
    </div>
    <!-- QUICK PERIODS END -->

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
            <h4 class="f-21 f-w-500 text-dark">@lang('Income Statement')</h4>
            <p class="f-14 text-dark-grey mb-0">
                {{ \Carbon\Carbon::parse($fromDate)->format(company()->date_format) }} -
                {{ \Carbon\Carbon::parse($toDate)->format(company()->date_format) }}
            </p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-light f-12 mr-2">
                {{ count($incomeStatement['revenue']) + count($incomeStatement['expenses']) }} @lang('Accounts')
            </span>
            @if($incomeStatement['net_income'] >= 0)
                <span class="badge badge-success f-12">@lang('Profit')</span>
            @else
                <span class="badge badge-danger f-12">@lang('Loss')</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-12">
            <x-cards.data padding="false" otherClasses="border-0">
                <!-- REVENUE SECTION -->
                <div class="border-bottom">
                    <div class="px-4 py-3 bg-light-success">
                        <h5 class="mb-0 f-16 f-w-500 text-success d-flex align-items-center">
                            <i class="fa fa-arrow-up mr-2"></i>@lang('Revenue')
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <tbody>
                                @forelse($incomeStatement['revenue'] as $revenue)
                                <tr>
                                    <td class="f-13 text-dark pl-4">{{ $revenue['account']->account_name }}</td>
                                    <td class="text-right f-13 f-w-500 text-success pr-4">{{ currency_format($revenue['balance']) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-lightest">
                                        <i class="fa fa-info-circle mr-1"></i>@lang('No revenue accounts found')
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light-success">
                                    <th class="f-14 f-w-500 text-success pl-4">@lang('Total Revenue')</th>
                                    <th class="text-right f-15 f-w-500 text-success pr-4">{{ currency_format($incomeStatement['total_revenue']) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- EXPENSES SECTION -->
                <div class="border-bottom">
                    <div class="px-4 py-3 bg-light-danger">
                        <h5 class="mb-0 f-16 f-w-500 text-danger d-flex align-items-center">
                            <i class="fa fa-arrow-down mr-2"></i>@lang('Expenses')
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <tbody>
                                @forelse($incomeStatement['expenses'] as $expense)
                                <tr>
                                    <td class="f-13 text-dark pl-4">{{ $expense['account']->account_name }}</td>
                                    <td class="text-right f-13 f-w-500 text-danger pr-4">{{ currency_format($expense['balance']) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-lightest">
                                        <i class="fa fa-info-circle mr-1"></i>@lang('No expense accounts found')
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light-danger">
                                    <th class="f-14 f-w-500 text-danger pl-4">@lang('Total Expenses')</th>
                                    <th class="text-right f-15 f-w-500 text-danger pr-4">{{ currency_format($incomeStatement['total_expenses']) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- NET INCOME SECTION -->
                <div class="px-4 py-4 {{ $incomeStatement['net_income'] >= 0 ? 'bg-light-success' : 'bg-light-danger' }}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0 f-18 f-w-500 {{ $incomeStatement['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                @if($incomeStatement['net_income'] >= 0)
                                    <i class="fa fa-trending-up mr-2"></i>@lang('Net Income')
                                @else
                                    <i class="fa fa-trending-down mr-2"></i>@lang('Net Loss')
                                @endif
                            </h4>
                            <p class="mb-0 f-13 text-dark-grey">
                                @if($incomeStatement['net_income'] >= 0)
                                    @lang('Company generated profit for this period')
                                @else
                                    @lang('Company incurred loss for this period')
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <h3 class="mb-0 f-21 f-w-500 {{ $incomeStatement['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ currency_format(abs($incomeStatement['net_income'])) }}
                            </h3>
                            <p class="mb-0 f-12 text-dark-grey">
                                {{ number_format(($incomeStatement['total_revenue'] > 0) ? (abs($incomeStatement['net_income']) / $incomeStatement['total_revenue']) * 100 : 0, 2) }}% @lang('of Revenue')
                            </p>
                        </div>
                    </div>
                </div>
            </x-cards.data>

            <!-- SUMMARY CARDS -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <x-cards.widget :title="__('Gross Revenue')" :value="currency_format($incomeStatement['total_revenue'])"
                        icon="arrow-up" iconColor="text-success" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-cards.widget :title="__('Total Expenses')" :value="currency_format($incomeStatement['total_expenses'])"
                        icon="arrow-down" iconColor="text-danger" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-cards.widget :title="$incomeStatement['net_income'] >= 0 ? __('Net Profit') : __('Net Loss')"
                        :value="currency_format(abs($incomeStatement['net_income']))"
                        :icon="$incomeStatement['net_income'] >= 0 ? 'trending-up' : 'trending-down'"
                        :iconColor="$incomeStatement['net_income'] >= 0 ? 'text-success' : 'text-danger'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize date range picker
    $('#dateRange').daterangepicker({
        locale: {
            format: '{{ company()->moment_date_format }}'
        },
        showDropdowns: true,
        autoUpdateInput: true
    });

    // Quick period selection
    $('#quick-period').on('change', function() {
        const period = $(this).val();
        if (period) {
            setQuickPeriod(period);
        }
    });

    // Apply filter
    $('#apply-filter').click(function() {
        const dateRange = $('#dateRange').val();
        const dates = dateRange.split(' - ');
        const url = "{{ route('accounting.reports.income-statement') }}";

        if (dates.length === 2) {
            const params = new URLSearchParams({
                from_date: dates[0],
                to_date: dates[1]
            });
            window.location.href = url + '?' + params.toString();
        }
    });

    // Set quick period dates
    function setQuickPeriod(period) {
        let startDate, endDate;
        const today = moment();

        switch(period) {
            case 'current_month':
                startDate = today.clone().startOf('month');
                endDate = today.clone().endOf('month');
                break;
            case 'last_month':
                startDate = today.clone().subtract(1, 'month').startOf('month');
                endDate = today.clone().subtract(1, 'month').endOf('month');
                break;
            case 'current_quarter':
                startDate = today.clone().startOf('quarter');
                endDate = today.clone().endOf('quarter');
                break;
            case 'last_quarter':
                startDate = today.clone().subtract(1, 'quarter').startOf('quarter');
                endDate = today.clone().subtract(1, 'quarter').endOf('quarter');
                break;
            case 'current_year':
                startDate = today.clone().startOf('year');
                endDate = today.clone().endOf('year');
                break;
            case 'last_year':
                startDate = today.clone().subtract(1, 'year').startOf('year');
                endDate = today.clone().subtract(1, 'year').endOf('year');
                break;
        }

        if (startDate && endDate) {
            $('#dateRange').data('daterangepicker').setStartDate(startDate);
            $('#dateRange').data('daterangepicker').setEndDate(endDate);
        }
    }

    // Export functions
    window.exportReport = function(format) {
        const dateRange = $('#dateRange').val();
        const dates = dateRange.split(' - ');
        const url = "{{ route('accounting.reports.income-statement') }}";

        if (dates.length === 2) {
            const params = new URLSearchParams({
                from_date: dates[0],
                to_date: dates[1],
                export: format
            });
            window.open(url + '?' + params.toString(), '_blank');
        }
    };

    // Print function
    window.printReport = function() {
        window.print();
    };

    $('.select-picker').selectpicker();
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

.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}
</style>
@endpush
