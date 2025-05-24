<div class="row">
    <div class="col-sm-12">
        <x-form id="save-event-registration-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.menu.AddEventRegistration')</h4>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="event_id" :fieldLabel="__('app.menu.events')" fieldName="event_id" search="true"
                            fieldRequired="true">
                            @foreach ($EvntEvent as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text fieldId="student_id" :fieldLabel="__('app.menu.studentId')" fieldName="student_id" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.studentId')">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile" :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">


                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode" search="true">
                                @foreach ($countries as $item)
                                    <option data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                        data-content="{{ $item->flagSpanCountryCode() }}" value="{{ $item->phonecode }}"
                                        {{ $item->phonecode == 971 ? 'selected' : '' }}>
                                        {{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                name="mobile" id="mobile">
                        </x-forms.input-group>
                    </div>
                </div>
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.number fieldId="no_of_participants" :fieldLabel="__('app.menu.noOfParticipants')" fieldName="no_of_participants"
                            fieldRequired="true" fieldValue="1" min="1">
                        </x-forms.number>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-event-registration-form-btn" class="mr-3"
                        icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('event-registration.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#save-event-registration-form-btn').click(function() {
            const url = "{{ route('event-registration.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-event-registration-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-event-registration-form-btn",
                data: $('#save-event-registration-form').serialize(),
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
