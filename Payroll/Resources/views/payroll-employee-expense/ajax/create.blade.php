<div class="row">
    <div class="col-sm-12">
        <x-form id="save-payroll-advance-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addemployeeExpense')
                </h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="expense_type" :fieldLabel="__('app.expensetype')" fieldName="expense_type" fieldRequired="true">
                            <option value="AdvanceSalary">@lang('app.advanceSalary')</option>
                            <option value="HRA">@lang('app.hra')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.select fieldId="employee_id" :fieldLabel="__('app.employee')" fieldName="employee_id">
                            <option value="">--</option>
                            @foreach ($employees as $item)
                                <x-user-option :user="$item" :selected="$loop->first" />
                            @endforeach
                        </x-forms.select>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="expense_date" :fieldLabel="__('app.expensedate')" fieldName="expense_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="amount" :fieldLabel="__('app.amount')" fieldName="amount" fieldRequired="true" :fieldPlaceholder="__('placeholders.amount')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="details" :fieldLabel="__('app.details')" fieldName="details" :fieldPlaceholder="__('placeholders.details')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="approval_status" :fieldLabel="__('app.approvalStatus')" fieldName="approval_status">
                            <option value="Pending">@lang('app.pending')</option>
                            <option value="Approved">@lang('app.approved')</option>
                            <option value="Rejected">@lang('app.rejected')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.datepicker fieldId="approval_date" :fieldLabel="__('app.approvalDate')" fieldName="approval_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div>
                </div>

                <div class="row px-4">
                    {{-- <div class="col-md-6">
                        <x-forms.datepicker fieldId="disbursement_date" :fieldLabel="__('app.disbursementDate')" fieldName="disbursement_date" :fieldPlaceholder="__('placeholders.date')" :fieldValue="now(company()->timezone)->translatedFormat(company()->date_format)" />
                    </div> --}}

                    <div class="col-md-6">
                        <x-forms.text fieldId="transaction_reference" :fieldLabel="__('app.transactionReference')" fieldName="transaction_reference" :fieldPlaceholder="__('placeholders.transactionReference')" />
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="payment_mode" :fieldLabel="__('app.paymentMode')" fieldName="payment_mode">
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->payment_method }}">{{ $method->payment_method }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.select fieldId="repayment_method" :fieldLabel="__('app.repaymentMethod')" fieldName="repayment_method">
                            <option value="One-time">@lang('app.oneTime')</option>
                            <option value="Installments">@lang('app.installments')</option>
                        </x-forms.select>
                    </div>
                </div>

                {{-- Installments Section --}}
                <div class="row px-4" id="installment-section" style="display: none;">
                    <div class="col-md-6 mb-3">
                        <x-forms.text fieldId="number_of_installments" :fieldLabel="__('app.numberOfInstallments')" fieldName="number_of_installments" fieldPlaceholder="Eg: 5" />
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="my-3 form-group">
                            <label class="mb-12 f-14 text-dark-grey w-100" for="salary_recovery">
                                @lang('app.salary_recovery')
                            </label>
                            <div class="d-flex">
                                <x-forms.checkbox fieldLabel="" fieldName="salary_recovery" fieldId="salary_recovery" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Installment Details Table --}}
                <div class="row px-4" id="installment-details" style="display: none;">
                    <div class="col-md-12">
                        <label class="mb-2 f-14 text-dark-grey">@lang('app.installmentDetails')</label>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('app.id')</th>
                                    <th>@lang('app.date')</th>
                                    <th>@lang('app.amount')</th>
                                </tr>
                            </thead>
                            <tbody id="installment-rows">
                                <!-- Installments will be generated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-payroll-advance" class="mr-3" icon="check">
                        @lang('app.save')
                    </x-forms.button-primary>

                    <x-forms.button-cancel :link="route('employee-expense.index')" class="border-0">
                        @lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

{{-- JS Section --}}
<script>
    $(document).ready(function() {
        ['#expense_date', '#approval_date', '#disbursement_date'].forEach(function(id) {
            $(id).each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });
        });

        $('#repayment_method').on('change', function() {
            if ($(this).val() === 'Installments') {
                $('#installment-section').show();
            } else {
                $('#installment-section').hide();
                $('#installment-details').hide();
                $('#installment-rows').html('');
            }
        });

        $('#salary_recovery').on('change', function() {
            if ($(this).is(':checked')) {
                const totalAmount = parseFloat($('#amount').val());
                const count = parseInt($('#number_of_installments').val());

                if (!isNaN(totalAmount) && !isNaN(count) && count > 0) {
                    generateInstallmentRows(count, totalAmount);
                    $('#installment-details').show();
                }
            } else {
                $('#installment-details').hide();
                $('#installment-rows').html('');
            }
        });

        $('#number_of_installments').on('input', function() {
            if ($('#salary_recovery').is(':checked')) {
                $('#salary_recovery').trigger('change');
            }
        });

        function generateInstallmentRows(count, total) {
            let tbody = '';
            let today = new Date();
            let monthlyAmount = (total / count).toFixed(2);

            for (let i = 0; i < count; i++) {
                let date = new Date(today.getFullYear(), today.getMonth() + i + 1, 0); // Last day of month
                let formattedDate = date.toISOString().split('T')[0];

                tbody += `<tr>
                            <td>${i + 1}</td>
                            <td><input type="date" name="installments[${i}][date]" value="${formattedDate}" class="form-control"></td>
                            <td><input type="text" name="installments[${i}][amount]" value="${monthlyAmount}" class="form-control"></td>
                          </tr>`;
            }

            $('#installment-rows').html(tbody);
        }

        $('#save-payroll-advance').click(function() {
            const url = "{{ route('employee-expense.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-payroll-advance-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-payroll-advance",
                data: $('#save-payroll-advance-form').serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
