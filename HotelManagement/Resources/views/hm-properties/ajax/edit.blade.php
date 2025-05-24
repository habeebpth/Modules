<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-property-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editProperty')</h4>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="property_name" :fieldLabel="__('app.PropertyName')"
                            fieldName="property_name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.propertyName')"
                            :fieldValue="$property->property_name">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="contact_number" :fieldLabel="__('modules.bankaccount.contactNumber')"
                            fieldName="contact_number" fieldRequired="false"
                            :fieldPlaceholder="__('placeholders.contactNumber')"
                            :fieldValue="$property->contact_number">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.email')"
                            :fieldValue="$property->email">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="website" :fieldLabel="__('app.website')" fieldName="website"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.website')"
                            :fieldValue="$property->website">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="address" :fieldLabel="__('app.address')" fieldName="address"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.address')"
                            :fieldValue="$property->address">
                        </x-forms.textarea>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="city" :fieldLabel="__('modules.stripeCustomerAddress.city')" fieldName="city"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.city')"
                            :fieldValue="$property->city">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="state" :fieldLabel="__('modules.stripeCustomerAddress.state')" fieldName="state"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.state')"
                            :fieldValue="$property->state">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    {{-- <div class="col-md-6">
                        <x-forms.text fieldId="country" :fieldLabel="__('app.country')" fieldName="country"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.country')"
                            :fieldValue="$property->country">
                        </x-forms.text>
                    </div> --}}
                    <div class="col-sm-12 col-md-6">
                        <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                        data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                        value="{{ $item->id }}" @if ($item->id == $property->country_id) selected @endif>{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="zip_code" :fieldLabel="__('app.zipCode')" fieldName="zip_code"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.zipCode')"
                            :fieldValue="$property->zip_code">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.text fieldId="location" :fieldLabel="__('app.location')" fieldName="location"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.location')"
                            :fieldValue="$property->location">
                        </x-forms.text>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-property-form" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('hm-properties.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>

        </x-form>

    </div>
</div>
<script>
    $(document).ready(function() {
        $('#update-property-form').click(function() {

            const url = "{{ route('hm-properties.update', $property->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-property-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-property-form",
                data: $('#edit-property-data-form').serialize(),
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
