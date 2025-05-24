<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-hotel-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editHotel')</h4>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')"
                            fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.propertyName')"
                            :fieldValue="$hotels->name">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="contact_number" :fieldLabel="__('modules.bankaccount.contactNumber')"
                            fieldName="contact_number" fieldRequired="false"
                            :fieldPlaceholder="__('placeholders.contactNumber')"
                            :fieldValue="$hotels->contact_number">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.email')"
                            :fieldValue="$hotels->email">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="total_rooms" :fieldLabel="__('app.totalRooms')" fieldName="total_rooms"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.total_rooms')"
                            :fieldValue="$hotels->total_rooms">
                        </x-forms.text>
                    </div>
                </div>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="price_per_night" :fieldLabel="__('app.PricePerNight')" fieldName="price_per_night"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.price_per_night')"
                            :fieldValue="$hotels->price_per_night">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="location" :fieldLabel="__('app.location')" fieldName="location"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.location')"
                            :fieldValue="$hotels->location">
                        </x-forms.text>
                    </div>
                </div>
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="amenities" :fieldLabel="__('app.amenities')" fieldName="amenities"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.amenities')"
                            :fieldValue="$hotels->amenities">
                        </x-forms.textarea>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="update-hotel-form" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('hotels.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>

        </x-form>

    </div>
</div>
<script>
    $(document).ready(function() {
        $('#update-hotel-form').click(function() {

            const url = "{{ route('hotels.update', $hotels->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-hotel-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-hotel-form",
                data: $('#edit-hotel-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });

        init(RIGHT_MODAL);
    });
</script>
