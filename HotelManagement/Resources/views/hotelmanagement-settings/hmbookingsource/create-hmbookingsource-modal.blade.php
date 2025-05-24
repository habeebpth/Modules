<x-form id="addHmBookingSource" method="POST" class="ajax-form" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.addBookingSource')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
                <!-- Name Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="name"
                        :fieldLabel="__('app.name')"
                        fieldName="name"
                        fieldRequired="true"
                    />
                </div>
                <!-- URL Field -->
             <div class="col-sm-12 col-md-6">
           <x-forms.text
                  fieldId="url"
                  :fieldLabel="__('app.url')"
                    fieldName="url"
                             />
                    </div>
                <!-- Description Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.textarea
                        fieldId="description"
                        :fieldLabel="__('app.description')"
                        fieldName="description"
                    />
                </div>

                <!-- Logo Upload Field -->
                <div class="col-lg-3">
                    <!-- Logo Upload Field -->
                    <x-forms.file
                        allowedFileExtensions="png jpg jpeg svg bmp"
                        class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('travels::app.travels.Logo')"
                        fieldName="logo[]"
                        fieldId="logo"
                        fieldHeight="119"
                        :popover="__('messages.fileFormat.ImageFile')"
                    />
                </div>

                <input type="hidden" name="company_id" value="{{ $company_id }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-booking-source" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // Save booking source
    $('.select-picker').selectpicker('refresh');
    $('#save-booking-source').click(function(e) {
        e.preventDefault();
        const form = $('#addHmBookingSource')[0];
        const formData = new FormData(form);

        // Validate mandatory fields
        let isValid = true;
        $('#addHmBookingSource').find('[fieldRequired="true"]').each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            toastr.error("@lang('messages.validationError')");
            return;
        }

        // Perform AJAX request
        $.ajax({
            url: "{{ route('hmbookingsource-settings.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#save-booking-source').prop('disabled', true).text('@lang("app.saving")...');
            },
            success: function(response) {
                if (response.status === "success") {
                    if ($('table#example').length) {
                        window.location.reload();
                    } else {
                        $('#source_id').html(response.data);
                        $('#source_id').selectpicker('refresh');
                        $(MODAL_LG).modal('hide');
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.error("@lang('messages.somethingWentWrong')");
            },
            complete: function() {
                $('#save-booking-source').prop('disabled', false).text('@lang("app.save")');
            }
        });
    });
</script>
