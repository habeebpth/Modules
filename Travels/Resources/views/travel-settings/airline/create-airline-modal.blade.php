<x-form id="addAirline" method="POST" class="ajax-form" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('travels::app.travels.addAirline')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
               <div class="col-sm-12 col-md-6">
                    <!-- Airline Name Field -->
                    <x-forms.text
                        fieldId="name"
                        :fieldLabel="__('app.name')"
                        fieldName="name"
                        fieldPlaceholder="e.g., Jonh Due"
                        fieldRequired="true"
                    />
                </div>
               <div class="col-sm-12 col-md-6">
                    <!-- Airline Code Field -->
                    <x-forms.text
                        fieldId="code"
                        :fieldLabel="__('app.code')"
                        fieldName="code"
                        fieldRequired="true"
                        fieldPlaceholder="e.g., EK"
                        maxlength="3"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                        @foreach ($countries as $item)
                            <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}">{{ $item->nicename }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
               <div class="col-sm-12 col-md-6">
                    <!-- Contact Number Field -->
                    <x-forms.text
                        fieldId="contact_number"
                        :fieldLabel="__('app.number')"
                        fieldName="contact_number"
                        fieldRequired="false"
                        fieldPlaceholder="+97142222222"
                    />
                </div>
               <div class="col-sm-12 col-md-6">
                    <!-- Website Field -->
                    <x-forms.text
                        fieldId="website"
                        :fieldLabel="__('app.website')"
                        fieldName="website"
                        fieldRequired="false"
                        fieldPlaceholder="https://www.example.com"
                    />
                </div>
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
        <x-forms.button-primary id="save-airline" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

    <script>
    // Save Airline
    $('.select-picker').selectpicker('refresh');
    $('#save-airline').click(function(e) {
        e.preventDefault();
        const form = $('#addAirline')[0];
        const formData = new FormData(form);

        // Validate mandatory fields
        let isValid = true;
        $('#addAirline').find('[fieldRequired="true"]').each(function() {
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

        // Perform AJAX request using jQuery's native ajax method
        $.ajax({
            url: "{{ route('travel-airline-settings.store') }}",
            type: "POST",
            data: formData,
            processData: false, // Important for file uploads
            contentType: false, // Important for file uploads
            beforeSend: function() {
                $('#save-airline').prop('disabled', true); // Disable the button to prevent multiple clicks
                $('#save-airline').text('@lang("app.saving")...');
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
                $('#save-airline').prop('disabled', false); // Re-enable the button
                $('#save-airline').text('@lang("app.save")');
            }
        });
    });
</script>

