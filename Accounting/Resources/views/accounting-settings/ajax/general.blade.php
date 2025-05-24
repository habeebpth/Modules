<div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
    @method('POST')
    <div class="row">
        <!-- Financial Year End -->
        <div class="col-md-4">
            <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                <x-forms.label fieldId="day" :fieldLabel="__('accounting::app.Accounting.financialYearEnd')">
                </x-forms.label>
            </div></div>
            <div class="col-md-6">
                <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                    <x-forms.label fieldId="currency_id" :fieldLabel="__('accounting::app.Accounting.Currency')">
                    </x-forms.label></div></div>
        </div>
    <div class="row">
        <!-- Financial Year End -->
        <div class="col-md-2">
            <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                <div class="select-others height-35 rounded">
                    <select class="form-control select-picker" name="day" id="day">
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                    {{ isset($financeSetting) && $financeSetting->financial_year_end->day == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

        <!-- Month Dropdown -->

                    <div class="col-md-2">
                        <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" name="month" id="month">
                    @foreach ([
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                        <option value="{{ $month }}"
                            {{ isset($financeSetting) && $financeSetting->financial_year_end->month == $index + 1 ? 'selected' : '' }}>
                            {{ $month }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

        <!-- Currency -->
        <div class="col-md-6">
            <div class="form-group c-inv-select mb-lg-0 mb-md-0 mb-4">
                <div class="select-others height-35 rounded">
                    <select class="form-control select-picker" name="currency_id" id="currency_id">
                        @foreach ($currencies as $currency)
                            <option @if ($financeSetting->currency_id == $currency->id) selected @endif value="{{ $currency->id }}"
                                    data-currency-code="{{$currency->currency_code}}">
                                {{ $currency->currency_code . ' (' . $currency->currency_symbol . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Buttons Start -->
<div class="w-100 border-top-grey">
    <x-setting-form-actions>
        <x-forms.button-primary id="save-finance-settings-form" class="mr-3" icon="check">@lang('app.save')
        </x-forms.button-primary>
    </x-setting-form-actions>
</div>
<!-- Buttons End -->

<script>
    // Save finance settings
    $('#save-finance-settings-form').click(function() {
        $.easyAjax({
            url: "{{ route('finance_settings.update', $financeSetting->id) }}",
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true,
            data: $('#editSettings').serialize(),
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-finance-settings-form",
        });
    });
</script>
