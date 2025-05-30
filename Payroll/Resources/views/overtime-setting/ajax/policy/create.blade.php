<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.add') @lang('payroll::app.menu.overtimePolicy')</h5>
    <button type="button" onclick="removeOpenModal()" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <x-form id="addOvertimePolicy" method="POST" class="ajax-form">
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true">
                        </x-forms.text>
                    </div>
                    {{-- <div class="col-lg-12">
                        <div class="form-group">
                            <x-forms.label fieldId="pay_code" :fieldLabel="__('payroll::app.menu.payCode')" fieldRequired="true">
                            </x-forms.label>
                            <select class="form-control select-picker" name="pay_code" id="pay_code"
                                data-live-search="true" data-size="8">
                                @foreach ($payCodes as $payCode)
                                    <option value="{{ $payCode->id }}">{{ $payCode->name }} ({{ $payCode->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                    <div class="col-lg-12">

                        <table class="table">
                            <tbody>
                                <tr style="height:">
                                    <td style="width: 60%">
                                        <x-forms.checkbox :fieldLabel="__('payroll::modules.payroll.policyWorkingDays')" fieldName="working_days"
                                            fieldId="working_days" fieldValue="yes" />
                                    </td>
                                    <td>

                                        <div class="form-group">

                                            <select class="form-control select-picker" name="pay_code_working_days" id="pay_code_working_days"
                                                data-live-search="true" data-size="8">
                                                @foreach ($payCodes as $payCode)
                                                    <option value="{{ $payCode->id }}">{{ $payCode->name }}
                                                        ({{ $payCode->code }})
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <x-forms.checkbox :fieldLabel="__('payroll::modules.payroll.policyWeekOffDays')" fieldName="week_end" fieldId="week_end"
                                            fieldValue="yes" />
                                    </td>
                                    <td>
                                        <div class="form-group">

                                            <select class="form-control select-picker" name="pay_code_week_end" id="pay_code_week_end"
                                                data-live-search="true" data-size="8">
                                                @foreach ($payCodes as $payCode)
                                                    <option value="{{ $payCode->id }}">{{ $payCode->name }}
                                                        ({{ $payCode->code }})
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <x-forms.checkbox :fieldLabel="__('payroll::modules.payroll.policyHolidayDays')" fieldName="holiday" fieldId="holiday"
                                            fieldValue="yes" />
                                    </td>
                                    <td>
                                        <div class="form-group">

                                            <select class="form-control select-picker" name="pay_code_holiday" id="pay_code_holiday"
                                                data-live-search="true" data-size="8">
                                                @foreach ($payCodes as $payCode)
                                                    <option value="{{ $payCode->id }}">{{ $payCode->name }}
                                                        ({{ $payCode->code }})
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- <div>
                        <br>&nbsp;
                    </div> --}}
                    <div class="col-lg-12 my-2 d-flex">
                        <div class="form-description text-dark-grey">
                            <p> {{ __('payroll::modules.payroll.before') }} </p>
                        </div>
                        <div class="input-radio-button mr-2 ml-2">
                            <select class="form-control" name="request_before_days" style="width:53px;">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option @if ($i == 15) selected @endif
                                        value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-description text-dark-grey">
                            <p>{{ __('payroll::modules.payroll.dayCurrentMonth') }}
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                    data-original-title="{{ __('payroll::messages.beforeDaysPopover') }}"></i>
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-12 my-2">
                        <x-forms.checkbox :fieldLabel="__('payroll::modules.payroll.allowReportingManager')" fieldName="allow_reporting_manager"
                            fieldId="allow_reporting_manager" fieldValue="yes" />
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="allow_roles" :fieldLabel="__('payroll::modules.payroll.allowRoles')">
                            </x-forms.label>
                            <select name="allow_roles[]" id="allow_roles" multiple class="form-control select-picker"
                                data-size="8">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
    <x-forms.button-primary id="savePolicy" icon="check">@lang('app.save')</x-forms.button-primary>
</div>

<script>
    $('#pay_code').selectpicker();
    $('#pay_code_working_days').selectpicker();
    $('#pay_code_week_end').selectpicker();
    $('#pay_code_holiday').selectpicker();

    $('#allow_roles').selectpicker();
    // save source
    $('#savePolicy').click(function(e) {
        e.preventDefault();

        $.easyAjax({
            url: "{{ route('overtime-policies.store') }}",
            container: '#addOvertimePolicy',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#savePolicy",
            data: $('#addOvertimePolicy').serialize(),
            success: function(response) {
                console.log();
                $('#savePayCode').prop("disabled", false);
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
