<x-form id="editfacilities" method="PUT" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.editFacility')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
                <div class="form-body">
                         <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="facility_name" :fieldLabel="__('hotelmanagement::app.hotelManagement.facilityName')"
                                      fieldName="facility_name" :fieldValue="$Facility->facility_name" fieldRequired="true" />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- Description Field -->
                        <x-forms.textarea fieldId="description" :fieldLabel="__('hotelmanagement::app.hotelManagement.description')"
                                          fieldName="description" :fieldValue="$Facility->description" fieldRequired="false" />
                    </div>
                </div>
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-facilities" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save channel
    $('#save-facilities').click(function() {
        $.easyAjax({
            url: "{{route('hm-facilities-settings.update', $Facility->id)}}",
            container: '#editfacilities',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-facilities",
            data: $('#editfacilities').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
