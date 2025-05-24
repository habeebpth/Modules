<link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-event-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.events.addEvent')</h4>
                <div class="row p-20">

                    <div class="col-lg-6 col-md-6">
                        <x-forms.label fieldId="event_name" fieldRequired="true"
                            :fieldLabel="__('modules.events.eventName')">
                        </x-forms.label>
                        <x-forms.input-group id="eventNameGroup">
                            <input type="text" class="form-control height-35 f-14"
                                name="event_name" id="event_name" required>
                        </x-forms.input-group>
                    </div>


                    <div class="col-lg-4 col-md-6">
                            <x-forms.label fieldId="colorselector" fieldRequired="true"
                                :fieldLabel="__('modules.tasks.labelColor')">
                            </x-forms.label>
                            <x-forms.input-group id="colorpicker">
                                <input type="text" class="form-control height-35 f-14"
                                    placeholder="{{ __('placeholders.colorPicker') }}" name="label_color"
                                    id="colorselector">

                                <x-slot name="append">
                                    <span class="input-group-text height-35 colorpicker-input-addon"><i></i></span>
                                </x-slot>
                            </x-forms.input-group>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group c-inv-select mb-4">
                            <x-forms.label fieldId="slug" fieldRequired="true" :fieldLabel="__('modules.events.slug')">
                            </x-forms.label>
                            <div class="select-others height-35 rounded">
                                <input type="text" class="form-control height-35 f-14" name="slug" id="slug"
                                    placeholder="Enter event slug" value="{{ old('slug', $event->slug ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group c-inv-select mb-4">
                            <x-forms.label fieldId="status" :fieldLabel="__('app.status')">
                            </x-forms.label>
                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" data-size="8"
                                    name="status" id="status">
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-yellow'></i> @lang('app.Upcoming')" value="Upcoming"></option>
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-light-green'></i> @lang('app.completed')" value="Completed"></option>
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-red'></i> @lang('app.cancelled')" value="Cancelled"></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group c-inv-select mb-4">
                            <x-forms.label fieldId="registration_link_enable" :fieldLabel="__('modules.events.enableRegistrationLink')" />
                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" data-size="8" name="registration_link_enable" id="registration_link_enable">
                                    <option data-content=" @lang('app.yes')" value="Y"
                                        {{ old('registration_link_enable', $event->registration_link_enable ?? 'N') == 'Y' ? 'selected' : '' }}>
                                    </option>
                                    <option data-content=" @lang('app.no')" value="N"
                                        {{ old('registration_link_enable', $event->registration_link_enable ?? 'N') == 'N' ? 'selected' : '' }}>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group c-inv-select mb-4">
                            <x-forms.label fieldId="registration_fees_enable" :fieldLabel="__('modules.events.enableRegistrationFees')" />
                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" data-size="8" name="registration_fees_enable" id="registration_fees_enable">
                                    <option data-content=" @lang('app.yes')" value="Y"
                                        {{ old('registration_fees_enable', $event->registration_fees_enable ?? 'N') == 'Y' ? 'selected' : '' }}>
                                    </option>
                                    <option data-content=" @lang('app.no')" value="N"
                                        {{ old('registration_fees_enable', $event->registration_fees_enable ?? 'N') == 'N' ? 'selected' : '' }}>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.datepicker fieldId="registration_last_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.registrationLastDate')" fieldName="registration_last_date"
                            :fieldValue="now(company()->timezone)->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.registrationLastTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="registration_last_time" fieldId="registration_last_time"
                                fieldRequired="true" />
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('modules.events.registrationFeesAmount')" fieldName="registration_fees_amount"
                            fieldId="registration_fees_amount" fieldType="number" fieldPlaceholder="0.00" step="0.01"
                            :fieldValue="old('registration_fees_amount', $event->registration_fees_amount ?? '')" />
                    </div>

                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="description" :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description"></div>
                            <textarea name="description" id="description-text" class="d-none"></textarea>
                        </div>
                    </div>
                    <input type = "hidden" name = "mention_user_ids" id = "mentionUserId" class ="mention_user_ids">

                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.startOnDate')" fieldName="start_date"
                            :fieldValue="now(company()->timezone)->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.startOnTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="start_time" fieldId="start_time"
                                fieldRequired="true" />
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.endOnDate')" fieldName="end_date"
                            :fieldValue="now(company()->timezone)->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.endOnTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="end_time" fieldId="end_time"
                                fieldRequired="true" />
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.noOfSeatsForGuests')" fieldName="no_of_seats_for_guests"
                            fieldId="no_of_seats_for_guests" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.guestSeatStart')" fieldName="guest_seat_start"
                            fieldId="guest_seat_start" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.guestSeatEnd')" fieldName="guest_seat_end"
                            fieldId="guest_seat_end" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.noOfSeatsForParticipants')" fieldName="no_of_seats_for_participants"
                            fieldId="no_of_seats_for_participants" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.participantsSeatStart')" fieldName="participants_seat_start"
                            fieldId="participants_seat_start" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.participantsSeatEnd')" fieldName="participants_seat_end"
                            fieldId="participants_seat_end" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.maximumParticipants')" fieldName="maximum_participants"
                            fieldId="maximum_participants" fieldPlaceholder="eg: 5" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.maximumParticipantsPerUser')" fieldName="maximum_participants_per_user"
                            fieldId="maximum_participants_per_user" fieldPlaceholder="eg: 10" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('modules.events.location')" fieldName="location"
                            fieldRequired="false" fieldId="location" fieldPlaceholder=" Location" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('modules.events.banner')" fieldName="banner" fieldId="banner"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('modules.events.icon')" fieldName="icon" fieldId="icon"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.file allowedFileExtensions="pdf doc docx" class="mr-0 mr-lg-2 mr-md-2"
                            :fieldLabel="__('modules.events.brocher')" fieldName="brocher" fieldId="brocher"
                            fieldHeight="119" :popover="__('messages.fileFormat.File')" />
                    </div>
                    <div class="col-lg-12 d-none">
                        <x-forms.file-multiple class="mr-0" :fieldLabel="__('app.menu.addFile')"
                            fieldName="file" fieldId="file-upload-dropzone" />
                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-event-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('tasks.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>

<script src="{{ asset('vendor/jquery/bootstrap-colorpicker.js') }}"></script>

<script>
  $('.select-picker').selectpicker('refresh');
    $('body').on('change', '#employee_department', function () {

        let departmentIds = $(this).val();
        if (departmentIds === '' || departmentIds.length === 0) {
            departmentIds = 0;
        }
        let userId = @json($projectTemplateMembers ?? []);
        let url = "{{ route('departments.members', ':id') }}";
        if (userId.length > 0) {
            url += "?userId=" + userId.join(",");
        }
        url = url.replace(':id', departmentIds);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#save-project-data-form',
            blockUI: true,
            redirect: true,
            success: function (data) {
                if (data.data && data.data.length > 0) {
                $('#selectAssignee').html(data.data);
                } else {
                    $('#selectAssignee').html('<option>No employees found</option>');
                }
                $('#selectAssignee').selectpicker('refresh');
            }
        });
    });

    function monthlyOn() {
        let ele = $('#monthlyOn');
        let url = '{{ route('events.monthly_on') }}';
        setTimeout(() => {
            $.easyAjax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    date: $('#start_date').val()
                },
                success: function(response) {
                    @if (App::environment('development'))
                        $('#event_name').val(response.message);
                        // $('#where').val(response.message);
                        $('#selectAssignee').val({{ user()->id }});
                        $('#selectAssignee').selectpicker('refresh');
                    @endif
                    ele.html(response.message);
                    $('#repeat_type').selectpicker('refresh');
                }
            });
        }, 100);

    }

    $(document).ready(function() {

        Dropzone.autoDiscover = false;
        //Dropzone class
        eventDropzone = new Dropzone("div#file-upload-dropzone", {
            dictDefaultMessage: "{{ __('app.dragDrop') }}",
            url: "{{ route('event-files.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            paramName: "file",
            maxFilesize: DROPZONE_MAX_FILESIZE,
            maxFiles: DROPZONE_MAX_FILES,
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: DROPZONE_MAX_FILES,
            acceptedFiles: DROPZONE_FILE_ALLOW,
            init: function() {
                eventDropzone = this;
            }
        });
        eventDropzone.on('sending', function(file, xhr, formData) {
            var eventID = $('#eventId').val();
            formData.append('eventId', eventID);
            $.easyBlockUI();
        });
        eventDropzone.on('uploadprogress', function() {
            $.easyBlockUI();
        });
        eventDropzone.on('queuecomplete', function() {
            window.location.href = '{{ route("events.index") }}';
        });
        eventDropzone.on('removedfile', function () {
            var grp = $('div#file-upload-dropzone').closest(".form-group");
            var label = $('div#file-upload-box').siblings("label");
            $(grp).removeClass("has-error");
            $(label).removeClass("is-invalid");
        });
        eventDropzone.on('error', function (file, message) {
            eventDropzone.removeFile(file);
            var grp = $('div#file-upload-dropzone').closest(".form-group");
            var label = $('div#file-upload-box').siblings("label");
            $(grp).find(".help-block").remove();
            var helpBlockContainer = $(grp);

            if (helpBlockContainer.length == 0) {
                helpBlockContainer = $(grp);
            }

            helpBlockContainer.append('<div class="help-block invalid-feedback">' + message + '</div>');
            $(grp).addClass("has-error");
            $(label).addClass("is-invalid");

        });

        $('#repeat-event').change(function() {
            $('.repeat-event-div').toggleClass('d-none');
            monthlyOn();
        })
        $('#send_reminder').change(function() {
            $('.send_reminder_div').toggleClass('d-none');
        })

        $('#start_time, #end_time, #registration_last_time').timepicker({
            @if (company()->time_format == 'H:i')
                showMeridian: false,
            @endif
        });

        $('#colorpicker').colorpicker({
            "color": "#ff0000"
        });

        $("#selectAssignee, #selectAssignee2, #selectHost, .multiple-users").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function(selected, total) {
                return selected + " {{ __('app.membersSelected') }} ";
            }
        });
        const atValues = @json($userData);

        quillMention(atValues, '#description');

        const dp1 = datepicker('#start_date', {
            position: 'bl',
            onSelect: (instance, date) => {
                if (typeof dp2.dateSelected !== 'undefined' && dp2.dateSelected.getTime() < date
                    .getTime()) {
                    dp2.setDate(date, true)
                }
                if (typeof dp2.dateSelected === 'undefined') {
                    dp2.setDate(date, true)
                }
                dp2.setMin(date);
                monthlyOn();
            },
            ...datepickerConfig
        });

        const dp2 = datepicker('#end_date', {
            position: 'bl',
            onSelect: (instance, date) => {
                dp1.setMax(date);
            },
            ...datepickerConfig
        });
        datepicker('#registration_last_date', {
            position: 'bl',
            onSelect: (instance, date) => {
                dp1.setMax(date);
            },
            ...datepickerConfig
        });

        $('#save-event-form').click(function() {
            var note = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = note;
            var mention_user_id = $('#description span[data-id]').map(function(){
                            return $(this).attr('data-id')
                        }).get();
            $('#mentionUserId').val(mention_user_id.join(','));

            const url = "{{ route('events.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-event-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-event-form",
                data: $('#save-event-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        if (eventDropzone.getQueuedFiles().length > 0) {
                        eventId = response.eventId
                        $('#eventId').val(eventId);
                        eventDropzone.processQueue();
                        }
                        if ($(MODAL_XL).hasClass('show')) {
                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else {
                            window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        monthlyOn();

        init(RIGHT_MODAL);
    });
</script>
