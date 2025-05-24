<div class="row">
    <div class="col-sm-12">
        <x-form id="save-transaction-form" enctype="multipart/form-data">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addRewardTransaction')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    {{-- Customer --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="customer_id" :fieldLabel="__('app.customer')" fieldName="customer_id"
                            fieldRequired="true" search="true">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    {{-- Transaction Type --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="transaction_type" :fieldLabel="__('app.transactionType')" fieldName="transaction_type"
                            fieldRequired="true">
                            <option value="Earn">@lang('app.earn')</option>
                            <option value="Redeem">@lang('app.redeem')</option>
                            <option value="Adjust">@lang('app.adjust')</option>
                        </x-forms.select>
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Points --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="points" :fieldLabel="__('app.points')" fieldName="points" fieldType="number"
                            fieldRequired="true" fieldValue="0" />
                    </div>

                    {{-- Transaction Date --}}
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="transaction_date" :fieldLabel="__('app.transactionDate')" fieldName="transaction_date"
                            :fieldPlaceholder="__('placeholders.date')" :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Reference Type --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="reference_type" :fieldLabel="__('app.referenceType')" fieldName="reference_type"
                            fieldRequired="true">
                            <option value="Purchase">Purchase</option>
                            <option value="Referral">Referral</option>
                            <option value="Manual">Manual</option>
                        </x-forms.select>
                    </div>

                    {{-- Reference ID --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="reference_id" :fieldLabel="__('app.referenceId')" fieldName="reference_id"
                            fieldType="number" :fieldPlaceholder="__('placeholders.transactionId')" />
                    </div>
                </div>

                <div class="row px-4">
                    {{-- Status --}}
                    <div class="col-md-6">
                        <x-forms.select fieldId="status" :fieldLabel="__('app.status')" fieldName="status">
                            <option value="active">@lang('app.active')</option>
                            <option value="hold">@lang('app.hold')</option>
                            <option value="expired">@lang('app.expired')</option>
                        </x-forms.select>
                    </div>

                    {{-- Earned From --}}
                    <div class="col-md-6">
                        <x-forms.text fieldId="earned_from" :fieldLabel="__('app.earnedFrom')" fieldName="earned_from"
                            fieldType="text" :fieldPlaceholder="__('placeholders.earnedFrom')" />
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="remarks" :fieldLabel="__('app.remarks')" fieldName="remarks"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.remarks')">
                        </x-forms.textarea>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-reward-transaction-form" class="mr-3"
                        icon="check">@lang('app.save')</x-forms.button-primary>
                    <x-forms.button-cancel :link="route('reward-transactions.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $('.select-picker').selectpicker('refresh');

    $(document).ready(function() {
        ['#transaction_date', '#end_date'].forEach(function(id) {
            $(id).each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });
        });
        $('#save-reward-transaction-form').click(function() {
            const url = "{{ route('reward-transactions.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-transaction-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: false,
                buttonSelector: "#save-reward-transaction-form",
                data: $('#save-transaction-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
    });
</script>
