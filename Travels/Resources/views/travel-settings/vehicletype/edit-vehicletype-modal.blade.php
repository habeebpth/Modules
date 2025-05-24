<x-form id="editVehicleType" method="PUT" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('travels::app.travels.editVehicleType')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="name" :fieldLabel="__('travels::app.travels.vehicletypeName')"
                                  fieldName="name" :fieldValue="$vehicletype->name" fieldRequired="true" />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.number fieldId="no_of_seats" :fieldLabel="__('travels::app.travels.noOfSeats')"
                                    fieldName="no_of_seats" :fieldValue="$vehicletype->no_of_seats" fieldRequired="true" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-vehicletype" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $('#save-vehicletype').click(function() {
        $.easyAjax({
            url: "{{route('vehicletype-settings.update', $vehicletype->id)}}",
            container: '#editVehicleType',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-vehicletype",
            data: $('#editVehicleType').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
