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
        <div class="col-md-12">
            <x-cards.data :title="__('Trial Balance')" padding="false">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>@lang('Account Code')</th>
                                <th>@lang('Account Name')</th>
                                <th class="text-right">@lang('Debit')</th>
                                <th class="text-right">@lang('Credit')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialBalance['accounts'] as $account)
                            <tr>
                                <td>{{ $account['account']->account_code }}</td>
                                <td>{{ $account['account']->account_name }}</td>
                                <td class="text-right">
                                    {{ $account['debit_balance'] > 0 ? currency_format($account['debit_balance']) : '-' }}
                                </td>
                                <td class="text-right">
                                    {{ $account['credit_balance'] > 0 ? currency_format($account['credit_balance']) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="2">@lang('Total')</th>
                                <th class="text-right">{{ currency_format($trialBalance['total_debits']) }}</th>
                                <th class="text-right">{{ currency_format($trialBalance['total_credits']) }}</th>
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
    const url = "{{ route('accounting.reports.trial-balance') }}";
    
    window.location.href = url + '?as_of_date=' + asOfDate;
});
</script>
@endpush