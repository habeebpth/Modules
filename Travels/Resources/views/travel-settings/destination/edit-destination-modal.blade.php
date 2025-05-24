<x-form id="editDestination" method="PUT" class="ajax-form" enctype="multipart/form-data">
    <div class="modal-header">
        <h5 class="modal-title" id="modelHeading">@lang('travels::app.travels.editDestination')</h5>
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
                        :fieldValue="$destination->name"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                        @foreach ($countries as $item)
                            <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}" @if ($item->id == $destination->country_id) selected @endif>{{ $item->nicename }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="city"
                        :fieldLabel="__('modules.stripeCustomerAddress.state')"
                        fieldName="city"
                        :fieldValue="$destination->city"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.text
                        fieldId="state"
                        :fieldLabel="__('modules.stripeCustomerAddress.city')"
                        fieldName="state"
                        :fieldValue="$destination->state"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.textarea
                        fieldId="description"
                        :fieldLabel="__('app.description')"
                        fieldName="description"
                        :fieldValue="$destination->description"
                    />
                </div>
                <div class="col-sm-12 col-md-6">
                    <x-forms.file
                        allowedFileExtensions="png jpg jpeg svg bmp"
                        class="mr-0 mr-lg-2 mr-md-2 cropper"
                        :fieldLabel="__('travels::app.travels.Image')"
                        fieldName="image[]"
                        fieldId="image"
                        fieldHeight="119"
                        :popover="__('messages.fileFormat.ImageFile')"
                    />
                    @if($destination->image)
                    <div class="mt-2">
                        <img src="{{ $destination->image_url }}" alt="{{ $destination->name }}" width="100">
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <x-forms.button-cancel data-dismiss="modal" class="border-0 mr-3">@lang('app.close')</x-forms.button-cancel>
        <x-forms.button-primary id="update-destination" icon="check">@lang('app.update')</x-forms.button-primary>
    </div>
</x-form>

<script>
    // Update destination
    $('.select-picker').selectpicker('refresh');
    $('#update-destination').click(function(e) {
        e.preventDefault();
        const form = $('#editDestination')[0];
        const formData = new FormData(form);

        // Validate mandatory fields
        let isValid = true;
        $('#editDestination').find('[fieldRequired="true"]').each(function() {
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
            url: "{{ route('destination-settings.update', $destination->id)}}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#update-destination').prop('disabled', true);
                $('#update-destination').text('@lang("app.updating")...');
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
                $('#update-destination').prop('disabled', false);
                $('#update-destination').text('@lang("app.update")');
            }
        });
    });
</script>
