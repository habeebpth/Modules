<div class="row">
    <div class="col-sm-12">
        <x-form id="update-guest-data-form" method="PUT" enctype="multipart/form-data">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.editGuest')</h4>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="first_name" :fieldLabel="__('app.firstName')"
                            fieldName="first_name" fieldRequired="true"
                            :fieldValue="$guest->first_name"
                            :fieldPlaceholder="__('placeholders.firstName')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="last_name" :fieldLabel="__('app.lastName')"
                            fieldName="last_name" fieldRequired="true"
                            :fieldValue="$guest->last_name"
                            :fieldPlaceholder="__('placeholders.lastName')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                            fieldRequired="true" :fieldValue="$guest->email"
                            :fieldPlaceholder="__('placeholders.email')">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile"
                            :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">
                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                search="true">
                                @foreach ($countries as $item)
                                    <option @selected($guest->country_phonecode == $item->phonecode && !is_null($item->numcode))
                                            data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                            data-content="{{$item->flagSpanCountryCode()}}"
                                            value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                   name="phone" id="mobile" value="{{ $guest->phone }}">
                        </x-forms.input-group>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6 col-lg-3">
                        <x-forms.datepicker fieldId="dob" fieldRequired="true" :fieldLabel="__('modules.employees.dateOfBirth')"
                            fieldName="dob" :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="($guest->dob ? \Carbon\Carbon::parse($guest->dob)->format(company()->date_format) : '')" />
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="gender" fieldRequired="true" :fieldLabel="__('app.gender')"
                            fieldName="gender">
                            <option value="male" {{ $guest->gender == 'male' ? 'selected' : '' }}>@lang('app.male')</option>
                            <option value="female" {{ $guest->gender == 'female' ? 'selected' : '' }}>@lang('app.female')</option>
                            <option value="others" {{ $guest->gender == 'others' ? 'selected' : '' }}>@lang('app.others')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="id_type" :fieldLabel="__('app.idType')" fieldName="id_type"
                            fieldRequired="true" :fieldValue="$guest->id_type"
                            :fieldPlaceholder="__('placeholders.idType')">
                        </x-forms.text>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.text fieldId="id_number" :fieldLabel="__('app.IdNumber')"
                            fieldName="id_number" fieldRequired="true"
                            :fieldValue="$guest->id_number"
                            :fieldPlaceholder="__('placeholders.IdNumber')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="address" :fieldLabel="__('app.address')" fieldName="address"
                            fieldRequired="true" :fieldValue="$guest->address"
                            :fieldPlaceholder="__('placeholders.address')">
                        </x-forms.textarea>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6 col-lg-6">
                        <x-forms.select fieldId="nationality_id" :fieldLabel="__('app.nationality')" fieldName="nationality_id"
                            search="true">
                            <option value="">--</option>
                            @foreach ($countries as $item)
                                <option @if ($guest->nationality_id == $item->id) selected @endif data-mobile="{{ $guest->mobile }}" data-tokens="{{ $item->iso3 }}" data-iso="{{ $item->iso }}" data-phonecode="{{ $item->phonecode }}" data-content="<span
                                class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span>
                            {{ $item->nicename }}" value="{{ $item->id }}">{{ $item->nicename }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('app.Idphoto')" fieldName="id_photo" fieldId="id_photo"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                    <div class="col-lg-3 col-md-6 mt-2">
                        <img src="{{ $guest->image_url }}" alt="{{ $guest->first_name }}" width="100">

                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-guest-form" class="mr-3" icon="check">@lang('app.update')
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
        $('#update-guest-form').click(function() {
            const url = "{{ route('hm-guests.update', $guest->id) }}";

            $.easyAjax({
                url: url,
                container: '#update-guest-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#update-guest-form",
                data: new FormData($('#update-guest-data-form')[0]),
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
