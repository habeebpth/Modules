<x-form id="editfloor" method="PUT" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.editFloor')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
                <div class="form-body">
                         <div class="row">
                    <!-- Floor Number Field -->
                    <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="floor_number" :fieldLabel="__('hotelmanagement::app.hotelManagement.floorNumber')"
                                      fieldName="floor_number" :fieldValue="$floor->floor_number" fieldRequired="true" />
                    </div>
                    <!-- Floor Name Field -->
                    <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="floor_name" :fieldLabel="__('hotelmanagement::app.hotelManagement.floorName')"
                                      fieldName="floor_name" :fieldValue="$floor->floor_name" fieldRequired="true" />
                    </div>
                </div>
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-floor" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save channel
    $('#save-floor').click(function() {
        $.easyAjax({
            url: "{{route('hotelmanagement-floor-settings.update', $floor->id)}}",
            container: '#editfloor',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-floor",
            data: $('#editfloor').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
