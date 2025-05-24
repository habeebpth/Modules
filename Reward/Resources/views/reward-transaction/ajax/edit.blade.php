<div class="row">
    <div class="col-sm-12">
        <x-form id="update-transaction-form" method="PUT"
            action="{{ route('reward-transactions.update', $transaction->id) }}">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editRewardTransaction')</h4>

                {{-- <input type="hidden" name="company_id" value="{{ $transaction->company_id }}">` --}}

                <div class="row px-4">
                    {{-- Customer --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="customer_id" :fieldLabel="__('app.customer')" fieldName="customer_id"
                            fieldRequired="true" search="true">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $transaction->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    {{-- Transaction Type --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="transaction_type" :fieldLabel="__('app.transactionType')" fieldName="transaction_type"
                            fieldRequired="true">
                            <option value="Earn" {{ $transaction->transaction_type == 'Earn' ? 'selected' : '' }}>
                                @lang('app.earn')</option>
                            <option value="Redeem" {{ $transaction->transaction_type == 'Redeem' ? 'selected' : '' }}>
                                @lang('app.redeem')</option>
                            <option value="Adjust" {{ $transaction->transaction_type == 'Adjust' ? 'selected' : '' }}>
                                @lang('app.adjust')</option>
                        </x-forms.select>
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Points --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="points" :fieldLabel="__('app.points')" fieldName="points" fieldType="number"
                            fieldRequired="true" fieldValue="{{ $transaction->points }}" />
                    </div>

                    {{-- Transaction Date --}}
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="transaction_date" :fieldLabel="__('app.transactionDate')" fieldName="transaction_date"
                            :fieldPlaceholder="__('placeholders.date')" :fieldValue="$transaction->transaction_date
                                ? \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat(
                                    company()->date_format,
                                )
                                : ''" />
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Reference Type --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="reference_type" :fieldLabel="__('app.referenceType')" fieldName="reference_type"
                            fieldRequired="true">
                            <option value="Purchase" {{ $transaction->reference_type == 'Purchase' ? 'selected' : '' }}>
                                Purchase</option>
                            <option value="Referral" {{ $transaction->reference_type == 'Referral' ? 'selected' : '' }}>
                                Referral</option>
                            <option value="Manual" {{ $transaction->reference_type == 'Manual' ? 'selected' : '' }}>
                                Manual</option>
                        </x-forms.select>
                    </div>

                    {{-- Reference ID --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="reference_id" :fieldLabel="__('app.referenceId')" fieldName="reference_id"
                            fieldType="number" fieldValue="{{ $transaction->reference_id }}" />
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Status --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status">
                            <option value="active" {{ $transaction->status == 'active' ? 'selected' : '' }}>
                                @lang('app.active')</option>
                            <option value="hold" {{ $transaction->status == 'hold' ? 'selected' : '' }}>
                                @lang('app.hold')</option>
                            <option value="expired" {{ $transaction->status == 'expired' ? 'selected' : '' }}>
                                @lang('app.expired')</option>
                        </x-forms.select>
                    </div>

                    {{-- Earned From --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="earned_from" :fieldLabel="__('app.earnedFrom')" fieldName="earned_from" fieldType="text"
                            fieldValue="{{ $transaction->earned_from }}" />
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldLabel="Remarks" fieldName="remarks" fieldId="remarks"
                            :fieldValue="$transaction->remarks" />
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-reward-transaction-form" class="mr-3"
                        icon="check">@lang('app.update')</x-forms.button-primary>
                    <x-forms.button-cancel :link="route('reward-transactions.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $('.select-picker').selectpicker('refresh');

    $(document).ready(function() {
        ['#transaction_date'].forEach(function(id) {
            $(id).each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });
        });

        $('#update-reward-transaction-form').click(function() {
            const url = $('#update-transaction-form').attr('action');

            $.easyAjax({
                url: url,
                container: '#update-transaction-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: false,
                buttonSelector: "#update-reward-transaction-form",
                data: $('#update-transaction-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
    });
</script>
