<x-form id="editHmBookingSource" method="PUT" class="ajax-form" enctype="multipart/form-data">
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('hotelmanagement::app.hotelManagement.editBookingSource')</h5>
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
                        :fieldValue="$hmbookingsources->name"
                    />
                </div>

                <!-- URL Field -->
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="url"
                        :fieldLabel="__('app.url')"
                        fieldName="url"
                        :fieldValue="$hmbookingsources->url"
                    />
                </div>
                  <!-- Description Field -->
                  <div class="col-sm-12 col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.description')"
                                fieldName="description" fieldId="description" :fieldPlaceholder="__('placeholders.description')"
                                :fieldValue="$hmbookingsources->description">
                            </x-forms.textarea>
                        </div>
                </div>
                  <!-- Logo Upload Field -->
                  <div class="col-lg-3">
                    <x-forms.file
                        allowedFileExtensions="png jpg jpeg svg bmp"
                        class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('travels::app.travels.Logo')"
                        fieldName="logo[]"
                        fieldId="logo"
                        fieldHeight="119"
                        :popover="__('messages.fileFormat.ImageFile')"
                        :fieldValue="$hmbookingsources->logo_url"
                    />
                    @if($hmbookingsources->logo)
                    <div class="mt-2">
                        <img src="{{ $hmbookingsources->image_url }}" alt="{{ $hmbookingsources->name }}" width="100">
                    </div>
                @endif
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="update-booking-source" icon="check">@lang('app.update')</x-forms.button-primary>
    </div>
</x-form>

<script>
    $('.select-picker').selectpicker('refresh');

    $('#update-booking-source').click(function(e) {
        e.preventDefault();
        const form = $('#editHmBookingSource')[0];
        const formData = new FormData(form);

        let isValid = true;
        $('#editHmBookingSource').find('[fieldRequired="true"]').each(function() {
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

        $.ajax({
            url: "{{ route('hmbookingsource-settings.update', $hmbookingsources->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#update-booking-source').prop('disabled', true).text('@lang("app.updating")...');
            },
            success: function(response) {
                if (response.status === "success") {
                    window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                toastr.error("@lang('messages.somethingWentWrong')");
            },
            complete: function() {
                $('#update-booking-source').prop('disabled', false).text('@lang("app.update")');
            }
        });
    });
</script>
