<x-form id="addacccategories" method="POST" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('accounting::app.Accounting.addacccategories')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
                <!-- Account Type Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="account_type_id" :fieldLabel="__('accounting::app.Accounting.accounttypes')"
                                    fieldName="account_type_id"  search="true" fieldRequired="true">
                        <option value="">@lang('app.select')</option>
                        @foreach($accountTypes as $accountType)
                            <option value="{{ $accountType->id }}">{{ $accountType->name }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <!-- Name Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="name" :fieldLabel="__('accounting::app.Accounting.name')"
                                  fieldName="name" fieldRequired="true" />
                </div>

                <!-- Code Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="code" :fieldLabel="__('accounting::app.Accounting.code')"
                                  fieldName="code" fieldRequired="true" />
                </div>

                <!-- Description Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.textarea fieldId="description" :fieldLabel="__('accounting::app.Accounting.description')"
                                      fieldName="description" />
                </div>

                <input type="hidden" name="company_id" value="{{ $company_id }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-acccategories" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save source
    $('.select-picker').selectpicker('refresh');
    $('#save-acccategories').click(function() {
        $.easyAjax({
            url: "{{ route('acc-categories-settings.store') }}",
            container: '#addacccategories',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-acccategories",
            data: $('#addacccategories').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    if($('table#example').length) {
                        window.location.reload();
                    }
                    else {
                        $('#source_id').html(response.data);
                        $('#source_id').selectpicker('refresh');
                        // $('#source_id').html(options);
                        // $('#source_id').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    }
                }
            }
        })
    });
</script>
