@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
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
        <div class="col-md-8 offset-md-2">
            <x-cards.data :title="__('Income Statement')" padding="false">
                <!-- REVENUE SECTION -->
                <div class="p-4 border-bottom">
                    <h5 class="mb-3 text-success">@lang('Revenue')</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($incomeStatement['revenue'] as $revenue)
                                <tr>
                                    <td>{{ $revenue['account']->account_name }}</td>
                                    <td class="text-right text-success">{{ currency_format($revenue['balance']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th>@lang('Total Revenue')</th>
                                    <th class="text-right text-success">{{ currency_format($incomeStatement['total_revenue']) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- EXPENSES SECTION -->
                <div class="p-4 border-bottom">
                    <h5 class="mb-3 text-danger">@lang('Expenses')</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($incomeStatement['expenses'] as $expense)
                                <tr>
                                    <td>{{ $expense['account']->account_name }}</td>
                                    <td class="text-right text-danger">{{ currency_format($expense['balance']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th>@lang('Total Expenses')</th>
                                    <th class="text-right text-danger">{{ currency_format($incomeStatement['total_expenses']) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- NET INCOME SECTION -->
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr class="bg-light">
                                    <td class="f-16 f-w-500">@lang('Total Revenue')</td>
                                    <td class="text-right f-16 text-success">{{ currency_format($incomeStatement['total_revenue']) }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="f-16 f-w-500">@lang('Total Expenses')</td>
                                    <td class="text-right f-16 text-danger">{{ currency_format($incomeStatement['total_expenses']) }}</td>
                                </tr>
                                <tr class="bg-primary text-white">
                                    <th class="f-18">@lang('Net Income')</th>
                                    <th class="text-right f-18 {{ $incomeStatement['net_income'] >= 0 ? 'text-white' : 'text-warning' }}">
                                        {{ currency_format($incomeStatement['net_income']) }}
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($incomeStatement['net_income'] < 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        <strong>@lang('Net Loss'):</strong> @lang('Total expenses exceed total revenue for this period.')
                    </div>
                    @else
                    <div class="alert alert-success mt-3">
                        <i class="fa fa-check-circle mr-2"></i>
                        <strong>@lang('Net Income'):</strong> @lang('Company generated profit for this period.')
                    </div>
                    @endif
                </div>
            </x-cards.data>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#apply-filter').click(function() {
    const fromDate = $('#from_date').val();
    const toDate = $('#to_date').val();
    const url = "{{ route('accounting.reports.income-statement') }}";

    window.location.href = url + '?from_date=' + fromDate + '&to_date=' + toDate;
});
</script>
@endpush
