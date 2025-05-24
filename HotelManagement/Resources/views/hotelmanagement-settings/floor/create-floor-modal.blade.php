<x-form id="addHmFloor" method="POST" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.addFloor')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body">
                <div class="row">
                    <!-- Floor Number Field -->
                    <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="floor_number" :fieldLabel="__('hotelmanagement::app.hotelManagement.floorNumber')"
                                      fieldName="floor_number" fieldRequired="true" />
                    </div>
                    <!-- Floor Name Field -->
                    <div class="col-sm-12 col-md-6">
                        <x-forms.text fieldId="floor_name" :fieldLabel="__('hotelmanagement::app.hotelManagement.floorName')"
                                      fieldName="floor_name" fieldRequired="true" />
                    </div>
                </div>
                <input type="hidden" name="company_id" value="{{ $company_id }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-floor" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save source
    $('#save-floor').click(function() {
        $.easyAjax({
            url: "{{ route('hotelmanagement-floor-settings.store') }}",
            container: '#addHmFloor',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-floor",
            data: $('#addHmFloor').serialize(),
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
