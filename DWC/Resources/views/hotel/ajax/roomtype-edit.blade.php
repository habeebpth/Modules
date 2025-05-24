<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<x-form id="edit-save-roomtype-data-form" method="PUT">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('app.EditRoomtype')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <x-forms.text :fieldLabel="__('app.roomtype')" fieldName="room_type" fieldRequired="true" fieldId="room_type"
                    :fieldPlaceholder="__('placeholders.roomtype')" />
            </div>
            <div class="col-md-6">
                <x-forms.text fieldId="max_occupancy" :fieldLabel="__('app.maxOccupancy')" fieldName="max_occupancy"
                    fieldRequired="false" :fieldPlaceholder="__('placeholders.max_occupancy')">
                </x-forms.text>
            </div>
            <div class="col-md-6">
                <x-forms.text fieldId="price_per_night" :fieldLabel="__('app.PricePerNight')" fieldName="price_per_night"
                    fieldRequired="false" :fieldPlaceholder="__('placeholders.price_per_night')">
                </x-forms.text>
            </div>
            <div class="col-md-12">
                <x-forms.textarea fieldId="amenities" :fieldLabel="__('app.amenities')" fieldName="amenities" fieldRequired="false"
                    :fieldPlaceholder="__('placeholders.amenities')">
                </x-forms.textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.cancel')</x-forms.button-cancel>
        <x-forms.button-primary id="edit-save-roomtype" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $(document).ready(function() {

        $('.select-picker').selectpicker();
        $('#edit-save-roomtype').click(function() {

            const url = "{{ route('sub-tasks.update', $roomtype->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-save-roomtype-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#edit-save-roomtype",
                data: $('#edit-save-roomtype-data-form').serialize(),
                success: function(response) {
                    if (response.status == "success") {

                            $('#room-type-list').html(response.view);
                            $(MODAL_LG).modal('hide');
                    }
                }
            });
        });

    });

</script>
