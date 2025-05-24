<div class="row">
    <div class="col-sm-12">
        <x-form id="save-hmroom-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.addRoom')</h4>

                <input type="hidden" name="company_id" value="{{ $company_id }}">

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.label class="my-3" fieldId="property_id"
                                       :fieldLabel="__('app.property')" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="property_id" id="hmroom_property_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($properties as $property)
                                    <option
                                        value="{{ $property->id }}"
                                        @if(isset($defaultAssignee) && $defaultAssignee == $property->id) selected @endif>
                                        {{ $property->property_name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-6">
                        <x-forms.label class="my-3" fieldId="floor_id"
                                       :fieldLabel="__('app.menu.floor')" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="floor_id" id="hmroom_floor_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($floors as $floor)
                                    <option
                                        value="{{ $floor->id }}">
                                        {{ $floor->floor_name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.label class="my-3" fieldId="room_type_id"
                                       :fieldLabel="__('app.menu.roomtype')" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="room_type_id" id="hmroom_room_type_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($roomTypes as $roomType)
                                    <option
                                        value="{{ $roomType->id }}">
                                        {{ $roomType->room_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="facilities_id" :fieldLabel="__('app.menu.facility')"
                                fieldRequired="true">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select class="form-control select-picker" multiple name="facilities_id[]"
                                    id="facilities_id" data-live-search="true" data-size="8">
                                    @foreach ($facilities as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->facility_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-forms.input-group>
                        </div>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('app.menu.roomNo')" fieldName="room_no"
                            fieldId="room_no" :fieldPlaceholder="__('placeholders.roomno')" fieldRequired="true" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('app.menu.roomSize')" fieldName="room_size"
                            fieldId="room_size" :fieldPlaceholder="__('placeholders.roomsize')" fieldRequired="true" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('app.menu.noOfBeds')" fieldName="no_of_beds"
                            fieldId="no_of_beds" :fieldPlaceholder="__('placeholders.noOfBeds')" fieldRequired="true" />
                    </div>
                </div>
                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="room_description" :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="room_description"></div>
                            <textarea name="room_description" id="description-text" class="d-none"></textarea>
                        </div>
                    </div>

                </div>

                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="room_conditions" :fieldLabel="__('app.menu.roomConditions')">
                            </x-forms.label>
                            <div id="room_conditions"></div>
                            <textarea name="room_conditions" id="conditions-text" class="d-none"></textarea>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-hmroom-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('hm-rooms.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>
    </div>
</div>
 <script>
$(document).ready(function() {
    quillMention(null, '#room_description');
    quillMention(null, '#room_conditions');

    $('#save-hmroom-form').click(function() {
        const descriptionContent = document.getElementById('room_description').children[0].innerHTML;
        document.getElementById('description-text').value = descriptionContent;

        const conditionsContent = document.getElementById('room_conditions').children[0].innerHTML;
        document.getElementById('conditions-text').value = conditionsContent;

        const url = "{{ route('hm-rooms.store') }}";
        $.easyAjax({
            url: url,
            container: '#save-hmroom-data-form',
            type: "POST",
            disableButton: true,
            blockUI: true,
            buttonSelector: "#save-hmroom-form",
            data: $('#save-hmroom-data-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    window.location.href = response.redirectUrl;
                }
            }
        });
    });

    init(RIGHT_MODAL);
});

</script>

