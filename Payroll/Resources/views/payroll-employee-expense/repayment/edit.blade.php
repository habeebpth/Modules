<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-repayment-form" method="PUT" enctype="multipart/form-data">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editEmployeExpenseRepayment')
                </h4>

                <input type="hidden" name="company_id" value="{{ $repayment->company_id }}">
                {{-- <input type="hidden" name="repayment_id" value="{{ $repayment->id }}">
                <input type="hidden" name="salary_advance_id" value="{{ $repayment->salary_advance_id }}"> --}}

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="due_date" fieldRequired="true" :fieldLabel="__('app.dueDate')"
                            fieldName="due_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="$repayment->due_date
                                ? date(company()->date_format, strtotime($repayment->due_date))
                                : ''" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="paid_date" fieldRequired="true" :fieldLabel="__('app.paidDate')"
                            fieldName="paid_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="$repayment->paid_date
                                ? date(company()->date_format, strtotime($repayment->paid_date))
                                : ''" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="advance_amount" :fieldLabel="__('app.PendingAmount')" fieldName="advance_amount"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.amount')" :fieldValue="$Pendingamount" :fieldReadOnly="false" />
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="amount" :fieldLabel="__('app.amount')" fieldName="amount" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.amount')" :fieldValue="$repayment->amount" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="repayment_method" :fieldLabel="__('app.repaymentMethod')" fieldName="repayment_method">
                            <option value="One-time" {{ $repayment->repayment_method == 'One-time' ? 'selected' : '' }}>
                                @lang('app.oneTime')</option>
                            <option value="Installments"
                                {{ $repayment->repayment_method == 'Installments' ? 'selected' : '' }}>@lang('app.installments')
                            </option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="transaction_reference" :fieldLabel="__('app.transactionReference')"
                            fieldName="transaction_reference" fieldRequired="false" :fieldPlaceholder="__('placeholders.transactionReference')"
                            :fieldValue="$repayment->transaction_reference" />
                    </div>

                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="payment_mode" :fieldLabel="__('app.paymentMode')" fieldName="payment_mode">
                            <option value="Cash" {{ $repayment->payment_mode == 'Cash' ? 'selected' : '' }}>
                                @lang('app.cash')</option>
                            <option value="Bank Transfer"
                                {{ $repayment->payment_mode == 'Bank Transfer' ? 'selected' : '' }}>@lang('app.bankTransfer')
                            </option>
                            <option value="UPI" {{ $repayment->payment_mode == 'UPI' ? 'selected' : '' }}>
                                @lang('app.upi')</option>
                            <option value="Cheque" {{ $repayment->payment_mode == 'Cheque' ? 'selected' : '' }}>
                                @lang('app.cheque')</option>
                            <option value="Other" {{ $repayment->payment_mode == 'Other' ? 'selected' : '' }}>
                                @lang('app.other')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('app.paymentProof')" fieldName="payment_proof" fieldId="payment_proof" fieldHeight="119"
                            :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                    <div class="col-lg-3 col-md-6 mt-2">
                        @if ($repayment->files->isNotEmpty())
                            <img src="{{ $repayment->files->first()->files_url }}" alt="Repayment Proof" width="200">
                        @else
                            <p>No file available</p>
                        @endif

                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-repayment" class="mr-3"
                        icon="check">@lang('app.update')</x-forms.button-primary>
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

    $('#update-repayment').click(function(e) {
        e.preventDefault(); // Prevent default form submission

        let pendingAmount = parseFloat("{{ $Pendingamount }}");
        let enteredAmount = parseFloat($('#amount').val());

        if (enteredAmount > pendingAmount) {
            Swal.fire({
                    icon: 'error',
                    title: "@lang('app.invalidAmount')",
                    text: "@lang('messages.invalidAmountMessage')"
                });
            return false; // Stop form submission
        }


        const url = "{{ route('employee-expense.repayment.update', $repayment->id) }}";
        $.easyAjax({
            url: url,
            container: '#edit-repayment-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            file: true,
            buttonSelector: "#update-repayment",
            data: new FormData($('#edit-repayment-form')[0]),
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
