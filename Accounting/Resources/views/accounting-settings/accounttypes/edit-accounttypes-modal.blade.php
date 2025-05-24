<x-form id="editacctype" method="PUT" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('accounting::app.Accounting.editAccountType')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
                <div class="form-body">
                         <div class="row">
                    <!-- Floor Number Field -->
                    <!-- Floor Name Field -->
                   <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('accounting::app.Accounting.name')"
                                      fieldName="name" :fieldValue="$accounttypes->name" fieldRequired="true" />
                    </div>
                   <div class="col-sm-12 col-md-6">
                        <x-forms.textarea fieldId="description" :fieldLabel="__('accounting::app.Accounting.description')"
                                          fieldName="description" :fieldValue="$accounttypes->description" />
                    </div>
                </div>
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-acctype" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save channel
    $('#save-acctype').click(function() {
        $.easyAjax({
            url: "{{route('acc-types-settings.update', $accounttypes->id)}}",
            container: '#editacctype',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-acctype",
            data: $('#editacctype').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
