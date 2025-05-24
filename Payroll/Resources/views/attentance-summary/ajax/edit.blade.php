<div class="row">
    <div class="col-sm-12">
        <x-form id="update-payroll-advance-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('payroll::app.menu.editAttentanceSummary')
                </h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="sl_full_pay" :fieldLabel="__('payroll::app.menu.slFullPay')"
                            fieldName="sl_full_pay" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.slFullPay')"
                            :fieldValue="$SalaryCalculation->sl_full_pay">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="sl_half_pay" :fieldLabel="__('payroll::app.menu.slHalfPay')"
                            fieldName="sl_half_pay" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.slHalfPay')"
                            :fieldValue="$SalaryCalculation->sl_half_pay">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="taken_leave" :fieldLabel="__('payroll::app.menu.takenLeave')"
                            fieldName="taken_leave" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.takenLeave')"
                            :fieldValue="$SalaryCalculation->taken_leave">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="absent" :fieldLabel="__('payroll::app.menu.absent')"
                            fieldName="absent" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.absent')"
                            :fieldValue="$SalaryCalculation->absent">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="combo_offs" :fieldLabel="__('payroll::app.menu.comboOffs')"
                            fieldName="combo_offs" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.comboOffs')"
                            :fieldValue="$SalaryCalculation->combo_offs">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="total_leave_earned" :fieldLabel="__('payroll::app.menu.totalLeaveEarned')"
                            fieldName="total_leave_earned" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.totalLeaveEarned')"
                            :fieldValue="$SalaryCalculation->total_leave_earned">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="opening_leave_balance" :fieldLabel="__('payroll::app.menu.openingLeaveBalance')"
                            fieldName="opening_leave_balance" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.openingLeaveBalance')"
                            :fieldValue="$SalaryCalculation->opening_leave_balance">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="closing_leave_balance" :fieldLabel="__('payroll::app.menu.closingLeaveBalance')"
                            fieldName="closing_leave_balance" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.closingLeaveBalance')"
                            :fieldValue="$SalaryCalculation->closing_leave_balance">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="opening_excess_leave" :fieldLabel="__('payroll::app.menu.openingExcessLeave')"
                            fieldName="opening_excess_leave" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.openingExcessLeave')"
                            :fieldValue="$SalaryCalculation->opening_excess_leave">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="closing_excess_leave" :fieldLabel="__('payroll::app.menu.closingExcessLeave')"
                            fieldName="closing_excess_leave" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.closingExcessLeave')"
                            :fieldValue="$SalaryCalculation->closing_excess_leave">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="excess_leave_taken" :fieldLabel="__('payroll::app.menu.excessLeaveTaken')"
                            fieldName="excess_leave_taken" fieldRequired="false" :fieldPlaceholder="__('payroll::app.menu.excessLeaveTaken')"
                            :fieldValue="$SalaryCalculation->excess_leave_taken">
                        </x-forms.text>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-payroll-advance" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('salary-advance.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#request_date').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });
        $('#approval_date').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });
        $('#disbursement_date').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $('#update-payroll-advance').click(function() {
            const url = "{{ route('attentance-summary.update', $SalaryCalculation->id) }}";

            $.easyAjax({
                url: url,
                container: '#update-payroll-advance-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-payroll-advance",
                data: $('#update-payroll-advance-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
        init(RIGHT_MODAL);
    });
</script>
