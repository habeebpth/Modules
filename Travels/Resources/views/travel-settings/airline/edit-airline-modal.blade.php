<x-form id="editAirline" method="PUT" class="ajax-form" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('travels::app.travels.editAirline')</h5>
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
                        fieldPlaceholder="e.g., Jonh Due"
                        :fieldValue="$airline->name"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="code"
                        :fieldLabel="__('app.code')"
                        fieldName="code"
                        fieldRequired="true"
                        fieldPlaceholder="e.g., EK"
                        maxlength="3"
                        :fieldValue="$airline->code"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                        @foreach ($countries as $item)
                            <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" @if ($item->id == $airline->country_id) selected @endif>{{ $item->nicename }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="contact_number"
                        :fieldLabel="__('app.number')"
                        fieldName="contact_number"
                        fieldRequired="false"
                        fieldPlaceholder="+97142222222"
                        :fieldValue="$airline->contact_number"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="website"
                        :fieldLabel="__('app.website')"
                        fieldName="website"
                        fieldRequired="false"
                        fieldPlaceholder="https://www.example.com"
                        :fieldValue="$airline->website"
                    />
                </div>
                <div class="col-lg-3">
                    <x-forms.file
                        allowedFileExtensions="png jpg jpeg svg bmp"
                        class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('travels::app.travels.Logo')"
                        fieldName="logo[]"
                        fieldId="logo"
                        fieldHeight="119"
                        :popover="__('messages.fileFormat.ImageFile')"
                        :fieldValue="$airline->logo_url"
                    />
                    @if($airline->logo)
                    <div class="mt-2">
                        <img src="{{ $airline->image_url }}" alt="{{ $airline->name }}" width="100">
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="save-airline" icon="check">@lang('app.save')</x-forms.button-primary>
    </div>
</x-form>
<script>
    // Update destination
    $('.select-picker').selectpicker('refresh');
    $('#save-airline').click(function(e) {
        e.preventDefault();
        const form = $('#editAirline')[0];
        const formData = new FormData(form);

        // Validate mandatory fields
        let isValid = true;
        $('#editAirline').find('[fieldRequired="true"]').each(function() {
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
            url: "{{ route('travel-airline-settings.update', $airline->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#save-airline').prop('disabled', true);
                $('#save-airline').text('@lang("app.updating")...');
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
                $('#save-airline').prop('disabled', false);
                $('#save-airline').text('@lang("app.update")');
            }
        });
    });
</script>
