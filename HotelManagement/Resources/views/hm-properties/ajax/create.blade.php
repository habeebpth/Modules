<div class="row">
    <div class="col-sm-12">
        <x-form id="save-property-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addProperty')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="property_name" :fieldLabel="__('app.PropertyName')"
                            fieldName="property_name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.propertyName')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="contact_number" :fieldLabel="__('modules.bankaccount.contactNumber')"
                            fieldName="contact_number" fieldRequired="false"
                            :fieldPlaceholder="__('placeholders.mobile')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.email')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="website" :fieldLabel="__('app.website')" fieldName="website"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.website')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="address" :fieldLabel="__('app.address')" fieldName="address"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.address')">
                        </x-forms.textarea>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="city" :fieldLabel="__('modules.stripeCustomerAddress.city')" fieldName="city"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.city')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="state" :fieldLabel="__('modules.stripeCustomerAddress.state')" fieldName="state"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.state')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    {{-- <div class="col-md-6">
                        <x-forms.text fieldId="country" :fieldLabel="__('app.country')" fieldName="country"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.country')">
                        </x-forms.text>
                    </div> --}}
                    <div class="col-sm-12 col-md-6">
                        <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country_id" search="true">
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}"
                                        data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                        value="{{ $item->id }}">{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="zip_code" :fieldLabel="__('app.zipCode')" fieldName="zip_code"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.zipCode')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.text fieldId="location" :fieldLabel="__('app.location')" fieldName="location"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.location')">
                        </x-forms.text>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-property-form" class="mr-3" icon="check">@lang('app.save')
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
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });
        $('#date').each(function (ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        $('#save-property-form').click(function() {

            const url = "{{ route('hm-properties.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-property-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-property-form",
                data: $('#save-property-data-form').serialize(),
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
