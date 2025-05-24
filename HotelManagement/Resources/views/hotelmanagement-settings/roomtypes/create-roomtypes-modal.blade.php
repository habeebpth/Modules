<x-form id="addroomtype" method="POST" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.addRoomType')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body">
                <div class="row">
                    <!-- Floor Number Field -->
                     <!-- Room Type Name Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text fieldId="room_type_name" :fieldLabel="__('hotelmanagement::app.hotelManagement.roomTypeName')"
                                  fieldName="room_type_name" fieldRequired="true" />
                </div>

                <!-- Max Occupancy Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.number fieldId="max_occupancy" :fieldLabel="__('hotelmanagement::app.hotelManagement.maxOccupancy')"
                                    fieldName="max_occupancy" fieldRequired="true" />
                </div>

                <!-- Base Price Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.number fieldId="base_price" :fieldLabel="__('hotelmanagement::app.hotelManagement.basePrice')"
                                    fieldName="base_price" fieldRequired="true" step="0.01" />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.textarea fieldId="description" :fieldLabel="__('hotelmanagement::app.hotelManagement.description')"
                                      fieldName="description" />
                </div>

                </div>
                <input type="hidden" name="company_id" value="{{ $company_id }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-roomtypes" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save source
    $('#save-roomtypes').click(function() {
        $.easyAjax({
            url: "{{ route('hm-roomtypes-settings.store') }}",
            container: '#addroomtype',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-roomtypes",
            data: $('#addroomtype').serialize(),
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
