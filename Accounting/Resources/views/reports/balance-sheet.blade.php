@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <x-forms.datepicker fieldId="as_of_date" :fieldLabel="__('As of Date')"
            fieldName="as_of_date" :fieldValue="$asOfDate" />
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
        <div class="col-md-6">
            <!-- ASSETS -->
            <x-cards.data :title="__('Assets')" padding="false">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @foreach($balanceSheet['assets'] as $asset)
                            <tr>
                                <td>{{ $asset['account']->account_name }}</td>
                                <td class="text-right">{{ currency_format($asset['balance']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>@lang('Total Assets')</th>
                                <th class="text-right">{{ currency_format($balanceSheet['total_assets']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>
        </div>
        
        <div class="col-md-6">
            <!-- LIABILITIES -->
            <x-cards.data :title="__('Liabilities')" padding="false">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @foreach($balanceSheet['liabilities'] as $liability)
                            <tr>
                                <td>{{ $liability['account']->account_name }}</td>
                                <td class="text-right">{{ currency_format($liability['balance']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>@lang('Total Liabilities')</th>
                                <th class="text-right">{{ currency_format($balanceSheet['total_liabilities']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>
            
            <!-- EQUITY -->
            <x-cards.data :title="__('Equity')" padding="false" otherClasses="mt-3">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            @foreach($balanceSheet['equity'] as $equity)
                            <tr>
                                <td>{{ $equity['account']->account_name }}</td>
                                <td class="text-right">{{ currency_format($equity['balance']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>@lang('Total Equity')</th>
                                <th class="text-right">{{ currency_format($balanceSheet['total_equity']) }}</th>
                            </tr>
                            <tr class="bg-primary text-white">
                                <th>@lang('Total Liabilities + Equity')</th>
                                <th class="text-right">{{ currency_format($balanceSheet['total_liabilities_equity']) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-cards.data>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#apply-filter').click(function() {
    const asOfDate = $('#as_of_date').val();
    const url = "{{ route('accounting.reports.balance-sheet') }}";
    
    window.location.href = url + '?as_of_date=' + asOfDate;
});
</script>
@endpush