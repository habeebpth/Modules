<div class="row">
    <div class="col-sm-12">
        <x-form id="save-guest-data-form"  enctype="multipart/form-data">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addGuest')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="first_name" :fieldLabel="__('app.firstName')"
                            fieldName="first_name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.firstName')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="last_name" :fieldLabel="__('app.lastName')"
                            fieldName="last_name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.lastName')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.email')">
                        </x-forms.text>
                    </div>
                    {{-- <div class="col-md-6">
                        <x-forms.text fieldId="phone" :fieldLabel="__('app.phone')" fieldName="phone"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.phone')">
                        </x-forms.text>
                    </div> --}}
                    {{-- <div class="col-lg-6 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile" fieldRequired="true"
                            :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">
                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                name="phone" id="mobile">
                        </x-forms.input-group>
                    </div> --}}

                    <div class="col-lg-6 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile"
                            :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">


                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                search="true">

                                @foreach ($countries as $item)
                                    <option data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                            data-content="{{$item->flagSpanCountryCode()}}"
                                            value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                                <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                name="phone" id="mobile">
                        </x-forms.input-group>
                    </div>

                </div>

                <div class="row px-4">
                    {{-- <div class="col-md-6">
                        <x-forms.datepicker fieldId="dob" :fieldLabel="__('app.dob')" fieldName="dob"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.dob')">
                        </x-forms.datepicker>
                    </div> --}}
                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="dob" fieldRequired="true" :fieldLabel="__('modules.employees.dateOfBirth')"
                            fieldName="dob" :fieldPlaceholder="__('placeholders.date')" />
                    </div>
                    {{-- <div class="col-md-6">
                        <x-forms.select fieldId="gender" :fieldLabel="__('app.gender')" fieldName="gender"
                            fieldRequired="false">
                            <option value="male">@lang('app.male')</option>
                            <option value="female">@lang('app.female')</option>
                            <option value="other">@lang('app.other')</option>
                        </x-forms.select>
                    </div> --}}
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="gender" fieldRequired="true" :fieldLabel="__('app.gender')"
                            fieldName="gender">
                            <option value="male">@lang('app.male')</option>
                            <option value="female">@lang('app.female')</option>
                            <option value="others">@lang('app.others')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="id_type" :fieldLabel="__('app.idType')" fieldName="id_type"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.idType')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="id_number" :fieldLabel="__('app.IdNumber')"
                            fieldName="id_number" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.IdNumber')" :popover="__('modules.employees.employeeIdHelp')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="address" :fieldLabel="__('app.address')" fieldName="address"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.address')">
                        </x-forms.textarea>
                    </div>
                </div>

                <div class="row px-4">
                    {{-- <div class="col-lg-6 col-md-6">
                        <x-forms.text fieldId="nationality" :fieldLabel="__('app.nationality')" fieldName="nationality"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.nationality')">
                        </x-forms.text>
                    </div> --}}

                    <div class="col-lg-6 col-md-6">
                        <x-forms.select fieldId="nationality_id" :fieldLabel="__('app.nationality')" fieldName="nationality_id"
                            search="true">
                            @foreach ($countries as $item)
                                <option data-tokens="{{ $item->iso3 }}" data-phonecode = "{{$item->phonecode}}" data-iso="{{ $item->iso }}"
                                    data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                    value="{{ $item->id }}">{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('app.Idphoto')" fieldName="id_photo" fieldId="id_photo"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-guest-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('hm-guests.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>

        </x-form>
    </div>
</div>

<script>
        $('.select-picker').selectpicker('refresh');
        datepicker('#dob', {
            position: 'bl',
            maxDate: new Date(),
            ...datepickerConfig
        });
    $(document).ready(function() {
        $('#save-guest-form').click(function() {
            const url = "{{ route('hm-guests.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-guest-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-guest-form",
                data: new FormData($('#save-guest-data-form')[0]),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    });
</script>
