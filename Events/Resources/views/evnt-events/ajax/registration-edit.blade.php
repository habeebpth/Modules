
<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-event-registration-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.menu.EditEventRegistration')</h4>
                    <input type="hidden" name="event_id" value="{{ $registration->event_id }}">
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.text fieldId="student_id" :fieldLabel="__('app.menu.studentId')" fieldName="student_id"
                            fieldRequired="true" :fieldPlaceholder="__('placeholders.studentId')"
                            fieldValue="{{ $registration->student_id }}">
                        </x-forms.text>
                    </div>
                    <div class="col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="__('app.name')" fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')" fieldValue="{{ $registration->name }}">
                        </x-forms.text>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.label class="my-3" fieldId="mobile"
                            :fieldLabel="__('app.mobile')"></x-forms.label>
                        <x-forms.input-group style="margin-top:-4px">
                            <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                search="true">
                                @foreach ($countries as $item)
                                    <option @selected($registration->country_phonecode == $item->phonecode && !is_null($item->numcode))
                                            data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                            data-content="{{$item->flagSpanCountryCode()}}"
                                            value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                            <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                   name="mobile" id="mobile" value="{{ $registration->mobile }}">
                        </x-forms.input-group>
                    </div>
                    <div class="col-md-6">
                        <x-forms.number fieldId="no_of_participants" :fieldLabel="__('app.menu.noOfParticipants')"
                            fieldName="no_of_participants" fieldRequired="true" fieldValue="{{ $registration->no_of_participants }}"
                            min="1">
                        </x-forms.number>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="edit-event-registration-form-btn" class="mr-3" icon="check">@lang('app.update')
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
        $('#edit-event-registration-form-btn').click(function() {
            const baseUrl = "{{ url('/') }}";
            const url = "{{ route('event-registration.update', $registration->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-event-registration-form',
                type: "PUT",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#edit-event-registration-form-btn",
                data: $('#edit-event-registration-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = `${baseUrl}/account/events/${response.event_id}?tab=event-registration`;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
