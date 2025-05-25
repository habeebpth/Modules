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
                            <x-forms.datepicker
                                fieldPlaceholder="@lang('Select Reconciliation Date')"
                                fieldId="reconciliation_date"
                                :fieldLabel="__('Reconciliation Date')"
                                fieldName="reconciliation_date"
                                fieldRequired="true" />
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
