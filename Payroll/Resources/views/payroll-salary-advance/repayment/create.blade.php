<div class="row">
    <div class="col-sm-12">
        <x-form id="save-repayment-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addSalaryAdvanceRepayment')
                </h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">
                <input type="hidden" name="salary_advance_id" value="{{ $salary_advance_id }}">
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="due_date" fieldRequired="true" :fieldLabel="__('app.dueDate')"
                            fieldName="due_date" :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="paid_date" fieldRequired="true" :fieldLabel="__('app.paidDate')"
                            fieldName="paid_date" :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="advance_amount" :fieldLabel="__('app.PendingAmount')" fieldName="advance_amount" fieldRequired="false"
                            :fieldPlaceholder="__('placeholders.amount')" :fieldValue="$Pendingamount" :fieldReadOnly="true" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="amount" :fieldLabel="__('app.amount')" fieldName="amount" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.amount')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="repayment_method" :fieldLabel="__('app.repaymentMethod')" fieldName="repayment_method">
                            <option value="One-time">@lang('app.oneTime')</option>
                            <option value="Installments">@lang('app.installments')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="transaction_reference" :fieldLabel="__('app.transactionReference')"
                            fieldName="transaction_reference" fieldRequired="false" :fieldPlaceholder="__('placeholders.transactionReference')" />
                    </div>

                </div>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="payment_mode" :fieldLabel="__('app.paymentMode')" fieldName="payment_mode">
                            <option value="Cash">@lang('app.cash')</option>
                            <option value="Bank Transfer">@lang('app.bankTransfer')</option>
                            <option value="UPI">@lang('app.upi')</option>
                            <option value="Cheque">@lang('app.cheque')</option>
                            <option value="Other">@lang('app.other')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('app.paymentProof')" fieldName="payment_proof" fieldId="payment_proof"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-repayment" class="mr-3" icon="check">@lang('app.save')</x-forms.button-primary>
                    <x-forms.button-cancel :link="route('salary-advance.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select-picker').selectpicker();
        $('#due_date, #paid_date').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $('#save-repayment').click(function(e) {
            e.preventDefault(); // Prevent form submission

            const pendingAmount = parseFloat("{{ $Pendingamount }}"); // Convert PendingAmount to a number
            const enteredAmount = parseFloat($('#amount').val()); // Get user input

            if (isNaN(enteredAmount) || enteredAmount <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: "@lang('app.invalidAmount')",
                    text: "@lang('messages.invalidAmountMessage')"
                });
                return;
            }

            if (enteredAmount > pendingAmount) {
                Swal.fire({
                    icon: 'error',
                    title: "@lang('app.invalidAmount')",
                    text: "@lang('messages.amountExceedsPending', ['pending' => $Pendingamount])"
                });
                return;
            }

            const url = "{{ route('salary-advance.repayment.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-repayment-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-repayment",
                data: new FormData($('#save-repayment-form')[0]),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });
    });
</script>

