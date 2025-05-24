<x-form id="save-attentance-summary-form" method="POST" class="ajax-form">
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
                                '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
                                '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
                                '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
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
                    <input type="hidden" name="department" id="department" value="all">
            <input type="hidden" name="designation" id="designation" value="all">
            <input type="hidden" name="userId" id="userId" value="all">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-attentance-summary" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // Save source
    $('.select-picker').selectpicker('refresh');
    $('#save-attentance-summary').click(function() {
        $.easyAjax({
            url: "{{ route('attentance-summary.store') }}",
            container: '#save-attentance-summary-form',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-attentance-summary",
            data: $('#save-attentance-summary-form').serialize(),
            success: function(response) {
                if (response.status == "success") {
                        window.location.reload();

                }
            }
        })
    });
</script>
