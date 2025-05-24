<x-form id="save-salary-calculation-form" method="POST" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('app.GenarateAttentanceSummary')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body">
                <div class="row">
                     <!-- Month Field -->
                     <div class="col-md-6">
                        <x-forms.select fieldId="month" :fieldLabel="__('accounting::app.Accounting.month')"
                                        fieldName="month" fieldRequired="true">
                            @foreach ([
                                '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                                '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                                '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                            ] as $key => $month)
                                <option value="{{ $key }}"
                                    {{ isset($financeSetting) && $financeSetting->financial_year_end->month == $key ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <!-- Year Field -->
                    <!-- Year Field using x-forms.text -->
                    <div class="col-md-6">
                        <x-forms.text fieldId="year" :fieldLabel="__('app.year')"
                                      fieldName="year" fieldType="number" fieldRequired="true"
                                      :fieldValue="isset($financeSetting) ? $financeSetting->financial_year_end->year : now()->year"
                                      fieldMin="1900" fieldMax="2099"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-salary-calculation" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // Save source
    $('.select-picker').selectpicker('refresh');
    $('#save-salary-calculation').click(function() {
        $.easyAjax({
            url: "{{ route('salary-calculation.store') }}",
            container: '#save-salary-calculation-form',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-salary-calculation",
            data: $('#save-salary-calculation-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                        window.location.reload();

                }
            }
        })
    });
</script>
