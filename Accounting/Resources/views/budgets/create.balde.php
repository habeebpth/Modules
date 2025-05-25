@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8">
            <x-cards.data :title="__('Create Budget')">
                <x-form id="budget-form">
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.select fieldId="fiscal_year_id" :fieldLabel="__('Fiscal Year')"
                                fieldName="fiscal_year_id" fieldRequired="true">
                                <option value="">@lang('Select Fiscal Year')</option>
                                @foreach($fiscalYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="col-md-6">
                            <x-forms.select fieldId="account_id" :fieldLabel="__('Account')"
                                fieldName="account_id" fieldRequired="true">
                                <option value="">@lang('Select Account')</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.select fieldId="period_type" :fieldLabel="__('Period Type')"
                                fieldName="period_type" fieldRequired="true">
                                <option value="">@lang('Select Period Type')</option>
                                <option value="monthly">@lang('Monthly')</option>
                                <option value="quarterly">@lang('Quarterly')</option>
                                <option value="yearly">@lang('Yearly')</option>
                            </x-forms.select>
                        </div>
                        <div class="col-md-6">
                            <x-forms.number fieldId="period_number" :fieldLabel="__('Period Number')"
                                fieldName="period_number" fieldRequired="true" :fieldValue="1" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.number fieldId="budgeted_amount" :fieldLabel="__('Budgeted Amount')"
                                fieldName="budgeted_amount" fieldRequired="true" step="0.01" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea fieldId="notes" :fieldLabel="__('Notes')"
                                fieldName="notes" />
                        </div>
                    </div>

                    <x-form-actions>
                        <x-forms.button-primary id="save-budget" class="mr-3" icon="check">@lang('app.save')
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('accounting.budgets.index')" class="border-0">@lang('app.cancel')
                        </x-forms.button-cancel>
                    </x-form-actions>
                </x-form>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#save-budget').click(function() {
    const url = "{{ route('accounting.budgets.store') }}";

    $.easyAjax({
        url: url,
        container: '#budget-form',
        type: "POST",
        data: $('#budget-form').serialize(),
        success: function(response) {
            if (response.status == 'success') {
                window.location.href = response.redirectUrl;
            }
        }
    });
});
</script>
@endsection
