<div class="row">
    <div class="col-sm-12">
        <x-form id="update-payroll-salary-calculation-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('payroll::app.menu.editSalaryCalculation')
                </h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_basic" :fieldLabel="__('payroll::app.menu.salaryBasic')"
                            fieldName="salary_basic" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryBasic')"
                            :fieldValue="$SalaryCalculation->salary_basic">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_spay" :fieldLabel="__('payroll::app.menu.salarySpay')"
                            fieldName="salary_spay" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salarySpay')"
                            :fieldValue="$SalaryCalculation->salary_spay">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_hra" :fieldLabel="__('payroll::app.menu.salaryHra')"
                            fieldName="salary_hra" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryHra')"
                            :fieldValue="$SalaryCalculation->salary_hra">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_incentive" :fieldLabel="__('payroll::app.menu.salaryIncentive')"
                            fieldName="salary_incentive" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryIncentive')"
                            :fieldValue="$SalaryCalculation->salary_incentive">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_gross" :fieldLabel="__('payroll::app.menu.salaryGross')"
                            fieldName="salary_gross" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryGross')"
                            :fieldValue="$SalaryCalculation->salary_gross">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_net" :fieldLabel="__('payroll::app.menu.salaryNet')"
                            fieldName="salary_net" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryNet')"
                            :fieldValue="$SalaryCalculation->salary_net">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_leave" :fieldLabel="__('payroll::app.menu.salaryLeave')"
                            fieldName="salary_leave" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryLeave')"
                            :fieldValue="$SalaryCalculation->salary_leave">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_advance" :fieldLabel="__('payroll::app.menu.salaryAdvance')"
                            fieldName="salary_advance" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryAdvance')"
                            :fieldValue="$SalaryCalculation->salary_advance">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_hra_advance" :fieldLabel="__('payroll::app.menu.salaryHraAdvance')"
                            fieldName="salary_hra_advance" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryHraAdvance')"
                            :fieldValue="$SalaryCalculation->salary_hra_advance">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="salary_ot" :fieldLabel="__('payroll::app.menu.salaryOt')"
                            fieldName="salary_ot" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.salaryOt')"
                            :fieldValue="$SalaryCalculation->salary_ot">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="total_deduction" :fieldLabel="__('payroll::app.menu.totalDeduction')"
                            fieldName="total_deduction" fieldRequired="false" :fieldPlaceholder="__('payroll::placeholders.totalDeduction')"
                            :fieldValue="$SalaryCalculation->total_deduction">
                        </x-forms.text>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="update-payroll-salary-calculation" class="mr-3" icon="check">
                        @lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('salary-advance.index')" class="border-0">
                        @lang('app.cancel')
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

        $('#update-payroll-salary-calculation').click(function() {
            const url = "{{ route('salary-calculation.update', $SalaryCalculation->id) }}";

            $.easyAjax({
                url: url,
                container: '#update-payroll-salary-calculation-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-payroll-salary-calculation",
                data: $('#update-payroll-salary-calculation-form').serialize(),
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
