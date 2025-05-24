<x-form id="addDestination" method="POST" class="ajax-form" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('travels::app.travels.adddestination')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="portlet-body">
            <div class="form-body row">
                    <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="name"
                        :fieldLabel="__('app.name')"
                        fieldName="name"
                        fieldRequired="true"
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
                    <x-forms.text
                        fieldId="city"
                        :fieldLabel="__('modules.stripeCustomerAddress.state')"
                        fieldName="city"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="state"
                        :fieldLabel="__('modules.stripeCustomerAddress.city')"
                        fieldName="state"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.textarea
                        fieldId="description"
                        :fieldLabel="__('app.description')"
                        fieldName="description"
                    />
                </div>
                <div class="col-lg-3">
                    <x-forms.file
                        allowedFileExtensions="png jpg jpeg svg bmp"
                        class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('travels::app.travels.Image')"
                        fieldName="image[]"
                        fieldId="image"
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
        <x-forms.button-primary id="save-destination" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // Save destination
    $('.select-picker').selectpicker('refresh');
    $('#save-destination').click(function(e) {
        e.preventDefault();
        const form = $('#addDestination')[0];
        const formData = new FormData(form);

        // Validate mandatory fields
        let isValid = true;
        $('#addDestination').find('[fieldRequired="true"]').each(function() {
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
            url: "{{ route('destination-settings.store') }}",
            type: "POST",
            data: formData,
            processData: false, // Important for file uploads
            contentType: false, // Important for file uploads
            beforeSend: function() {
                $('#save-destination').prop('disabled', true); // Disable the button to prevent multiple clicks
                $('#save-destination').text('@lang("app.saving")...');
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
                $('#save-destination').prop('disabled', false); // Re-enable the button
                $('#save-destination').text('@lang("app.save")');
            }
        });
    });
</script>
