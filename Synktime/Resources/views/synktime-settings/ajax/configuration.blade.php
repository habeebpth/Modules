<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
    @method('POST')
    <div class="row">
        <!-- URL -->
        <div class="col-md-6">
            <x-forms.text fieldId="url" :fieldLabel="__('app.url')" fieldName="url" fieldRequired="false" :fieldPlaceholder="__('placeholders.url')"
                :fieldValue="$configurations->url ?? ''">
            </x-forms.text>
        </div>

        <!-- API Key -->
        <div class="col-md-6">
            <x-forms.text fieldId="api_key" :fieldLabel="__('app.apiKey')" fieldName="api_key" fieldRequired="false" :fieldPlaceholder="__('placeholders.apiKey')"
                :fieldValue="$configurations->api_key ?? ''">
            </x-forms.text>
        </div>
    </div>

    <div class="row">
        <!-- Username -->
        <div class="col-md-6">
            <x-forms.text fieldId="username" :fieldLabel="__('app.username')" fieldName="username" fieldRequired="false"
                :fieldPlaceholder="__('placeholders.username')" :fieldValue="$configurations->username ?? ''">
            </x-forms.text>
        </div>

        <!-- Password -->
        <div class="col-md-6">
            <x-forms.text fieldId="password" :fieldLabel="__('app.password')" fieldName="password" fieldRequired="false"
                type="text" :fieldPlaceholder="__('placeholders.password')" :fieldValue="$configurations->password ?? ''">
            </x-forms.text>
        </div>
    </div>

    <div class="row">
        <!-- Attendance Type -->
        <div class="col-md-6">
            <x-forms.select fieldId="attendance_type" :fieldLabel="__('app.attendanceType')" fieldName="attendance_type">
                <option value="transaction"
                    {{ !empty($configurations) && $configurations->attendance_type == 'transaction' ? 'selected' : '' }}>
                    Transaction
                </option>
                <option value="summary"
                    {{ !empty($configurations) && $configurations->attendance_type == 'summary' ? 'selected' : '' }}>
                    Summary
                </option>
            </x-forms.select>
        </div>
        <!-- Day Change Time -->
        {{-- <div class="col-md-6">
            <x-forms.text fieldId="day_change_time" :fieldLabel="__('app.dayChangeTime')"
                fieldName="day_change_time" fieldRequired="false"
                :fieldPlaceholder="__('app.dayChangeTime')"
                :fieldValue="$configurations->day_change_time ?? ''">
            </x-forms.text>
        </div> --}}
        <div class="col-lg-6 col-md-6">
            <div class="bootstrap-timepicker timepicker">
                <x-forms.text class="a-timepicker" :fieldLabel="__('app.dayChangeTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="day_change_time"
                    fieldId="day_change_time" fieldRequired="true" :fieldValue="!is_null($configurations->day_change_time)
                        ? \Carbon\Carbon::createFromFormat('H:i:s', $configurations->day_change_time)->format('h:i A')
                        : ''" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="bootstrap-timepicker timepicker">
                <x-forms.text class="a-timepicker" :fieldLabel="__('app.defaultStartTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="default_start_time"
                    fieldId="default_start_time" fieldRequired="true" :fieldValue="!is_null($configurations->default_start_time)
                        ? \Carbon\Carbon::createFromFormat('H:i:s', $configurations->default_start_time)->format(
                            'h:i A',
                        )
                        : ''" />
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="bootstrap-timepicker timepicker">
                <x-forms.text class="a-timepicker" :fieldLabel="__('app.defaultEndTime')" :fieldPlaceholder="__('placeholders.hours')" fieldName="default_end_time"
                    fieldId="default_end_time" fieldRequired="true" :fieldValue="!is_null($configurations->default_end_time)
                        ? \Carbon\Carbon::createFromFormat('H:i:s', $configurations->default_end_time)->format('h:i A')
                        : ''" />
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Default Working Time (Minutes) -->
        <div class="col-md-6">
            <x-forms.number fieldId="default_working_time" :fieldLabel="__('app.defaultWorkingTime')" fieldName="default_working_time"
                fieldRequired="true" fieldValue="{{ $configurations->default_working_time ?? '' }}" min="1">
            </x-forms.number>
        </div>

        <!-- Proper Checkin-Checkout -->
        <div class="col-md-6">
            <x-forms.select fieldId="proper_checkin_checkout" :fieldLabel="__('app.properCheckinCheckout')" fieldName="proper_checkin_checkout">
                <option value="1" {{ $configurations->proper_checkin_checkout == 1 ? 'selected' : '' }}>Yes
                </option>
                <option value="0" {{ $configurations->proper_checkin_checkout == 0 ? 'selected' : '' }}>No</option>
            </x-forms.select>
        </div>
    </div>
    <div class="row">
        <!-- Salary At Month End -->
        <div class="col-md-6">
            <x-forms.select fieldId="salary_at_month_end" :fieldLabel="__('app.salaryAtMonthEnd')" fieldName="salary_at_month_end">
                <option value="1" {{ $configurations->salary_at_month_end == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ $configurations->salary_at_month_end == 0 ? 'selected' : '' }}>No</option>
            </x-forms.select>
        </div>
        <!-- Salary Date (if not end of month) -->
        <div class="col-md-6" id="salary-date-row"
            style="{{ $configurations->salary_at_month_end == 0 ? '' : 'display:none;' }}">
            <x-forms.datepicker fieldId="salary_date" :fieldLabel="__('app.endDate')" fieldName="salary_date" :fieldPlaceholder="__('placeholders.date')"
            :fieldValue="$configurations->salary_date
                ? \Carbon\Carbon::parse($configurations->salary_date)
                    ->timezone(company()->timezone)
                    ->translatedFormat(company()->date_format)
                : ''" />
        </div>
    </div>

    <!-- Buttons Start -->
    <div class="w-100 border-top-grey">
        <x-setting-form-actions>
            <x-forms.button-primary id="save-configuration-form" class="mr-3" icon="check">@lang('app.save')
            </x-forms.button-primary>
        </x-setting-form-actions>
    </div>
    <!-- Buttons End -->

    <script>
        $(document).ready(function() {
            // Toggle Salary Date Row based on selected option
            $('#salary_at_month_end').on('change', function() {
                if ($(this).val() == '0') {
                    $('#salary-date-row').show();
                } else {
                    $('#salary-date-row').hide();
                }
            });

            $('#day_change_time').timepicker({
                @if (company()->time_format == 'H:i')
                    showMeridian: false,
                @endif
                minuteStep: 1
            });
            $('#default_end_time').timepicker({
                @if (company()->time_format == 'H:i')
                    showMeridian: false,
                @endif
                minuteStep: 1
            });
            $('#default_start_time').timepicker({
                @if (company()->time_format == 'H:i')
                    showMeridian: false,
                @endif
                minuteStep: 1
            });
            ['#salary_date', '#end_date'].forEach(function(id) {
                $(id).each(function(ind, el) {
                    datepicker(el, {
                        position: 'bl',
                        ...datepickerConfig
                    });
                });
            });
        });

        // Ensure configurations ID exists before calling AJAX
        var configId = "{{ $configurations->id ?? '' }}";

        $('#save-configuration-form').click(function() {
            $.easyAjax({
                url: configId ?
                    "{{ route('synktime-settings.updateconfiguration', $configurations->id ?? '') }}" :
                    "{{ route('synktime-settings.updateconfiguration') }}",
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#editSettings').serialize(),
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-configuration-form",
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });
        });
    </script>
