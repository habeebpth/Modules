
<div class="row">
    <div class="col-sm-12">
        <x-form id="edit-event-participant-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('app.menu.EditEventParticipant')</h4>
                    <input type="hidden" name="event_id" value="{{ $participant->event_id }}">
                <div class="row px-4">
                    <div class="col-md-6">
                        <x-forms.select fieldId="event_registration_id" :fieldLabel="__('app.menu.events')" fieldName="event_registration_id" search="true" fieldRequired="true">
                            @foreach ($registrations as $registration)
                                <option value="{{ $registration->id }}" {{ $participant->event_registration_id == $registration->id ? 'selected' : '' }}>
                                    {{ $registration->name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6">
                        <x-forms.number fieldId="no_of_participants" :fieldLabel="__('app.menu.noOfParticipants')"
                            fieldName="no_of_participants" fieldRequired="true" fieldValue="{{ $participant->no_of_participants }}"
                            min="1">
                        </x-forms.number>
                    </div>
                </div>

                <div class="row px-4">
                    <div class="col-lg-6 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.NoOfSeatsFilledStart')" fieldName="no_of_seats_filled_start"
                            fieldId="no_of_seats_filled_start"  :fieldValue="$participant->no_of_seats_filled_start" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.NoOfSeatsFilledEnd')" fieldName="no_of_seats_filled_end"
                            fieldId="no_of_seats_filled_end"  :fieldValue="$participant->no_of_seats_filled_end" fieldPlaceholder="eg: 5" />
                    </div>

                </div>
                <div class="row px-4">
                    <div class="col-md-12">
                        <x-forms.textarea fieldId="remarks" :fieldLabel="__('app.remarks')" fieldName="remarks"
                            fieldRequired="false" :fieldPlaceholder="__('placeholders.remarks')"
                            :fieldValue="$participant->remarks">
                        </x-forms.textarea>
                    </div>
                </div>
                <x-form-actions>
                    <x-forms.button-primary id="edit-event-participant-form-btn" class="mr-3" icon="check">@lang('app.update')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('events.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#edit-event-participant-form-btn').click(function() {
            const baseUrl = "{{ url('/') }}";
            const url = "{{ route('event.event-participant.update', $participant->id) }}";

            $.easyAjax({
                url: url,
                container: '#edit-event-participant-form',
                type: "PUT",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#edit-event-participant-form-btn",
                data: $('#edit-event-participant-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = `${baseUrl}/account/events/${response.event_id}?tab=event-participant`;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>
