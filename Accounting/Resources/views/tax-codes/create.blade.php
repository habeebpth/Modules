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
