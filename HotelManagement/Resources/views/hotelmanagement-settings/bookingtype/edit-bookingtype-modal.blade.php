<x-form id="editbookingtype" method="PUT" class="ajax-form">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.editbookingtype')</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
                <div class="form-body">
                         <div class="row">
                    <!-- Floor Name Field -->
                    <div class="col-sm-12 col-md-12">
                        <x-forms.text fieldId="name" :fieldLabel="__('hotelmanagement::app.hotelManagement.bookingtypeName')"
                                      fieldName="name" :fieldValue="$bookingtype->name" fieldRequired="true" />
                    </div>
                </div>
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-bookingtype" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // save channel
    $('#save-bookingtype').click(function() {
        $.easyAjax({
            url: "{{route('hmbookingtype-settings.update', $bookingtype->id)}}",
            container: '#editbookingtype',
            type: "POST",
            blockUI: true,
            disableButton: true,
            buttonSelector: "#save-bookingtype",
            data: $('#editbookingtype').serialize(),
            success: function(response) {
                if (response.status == "success") {
                    window.location.reload();
                }
            }
        })
    });
</script>
