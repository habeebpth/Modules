
<link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-colorpicker.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-event-data-form" method="POST" action="{{ route('events.update', $event->id) }}">
            @csrf
            @method('PUT')
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang('modules.events.editEvent')</h4>
                <div class="row p-20">
                    <input type = "hidden" name = "mention_user_ids" id = "mentionUserId" class ="mention_user_ids">
                    <div class="col-lg-6 col-md-6">
                        <x-forms.label fieldId="event_name" fieldRequired="true"
                            :fieldLabel="__('modules.events.eventName')">
                        </x-forms.label>
                        <x-forms.input-group id="eventNameGroup">
                            <input type="text" class="form-control height-35 f-14"
                                name="event_name" id="event_name" value="{{ $event->name ?? '' }}" required>
                        </x-forms.input-group>
                    </div>
                    <div class="col-lg-4 col-md-6">
                            <x-forms.label fieldId="colorselector" fieldRequired="true"
                                :fieldLabel="__('modules.tasks.labelColor')">
                            </x-forms.label>
                            <x-forms.input-group id="colorpicker">
                                <input type="text" class="form-control height-35 f-14" name="label_color" id="colorselector" value="{{ $event->label_color }}">
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
                        <div class="form-group c-inv-select mb-4 my-3">
                            <x-forms.label fieldId="status" :fieldLabel="__('app.status')">
                            </x-forms.label>
                            <div class="select-others height-35 rounded">
                                <select class="form-control select-picker" data-live-search="true" data-size="8"
                                    name="status" id="status">
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-yellow'></i> @lang('app.Upcoming')" value="Upcoming" @if ($event->status == 'Upcoming') selected @endif></option>
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-light-green'></i> @lang('app.completed')" value="completed" @if ($event->status == 'completed') selected @endif></option>
                                    <option data-content="<i class='fa fa-circle mr-1 f-15 text-red'></i> @lang('app.cancelled')" value="cancelled" @if ($event->status == 'cancelled') selected @endif></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select :fieldLabel="__('modules.events.enableRegistrationLink')" fieldName="registration_link_enable" fieldId="registration_link_enable">
                            <option value="Y" {{ $event->registration_link_enable == 'Y' ? 'selected' : '' }}>@lang('app.yes')</option>
                            <option value="N" {{ $event->registration_link_enable == 'N' ? 'selected' : '' }}>@lang('app.no')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select :fieldLabel="__('modules.events.enableRegistrationFees')" fieldName="registration_fees_enable" fieldId="registration_fees_enable">
                            <option value="Y" {{ $event->registration_fees_enable == 'Y' ? 'selected' : '' }}>@lang('app.yes')</option>
                            <option value="N" {{ $event->registration_fees_enable == 'N' ? 'selected' : '' }}>@lang('app.no')</option>
                        </x-forms.select>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <x-forms.datepicker fieldId="registration_last_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.registrationLastDate')" fieldName="registration_last_date"
                            :fieldValue="(($event->registration_last_date_time) ? date( company()->date_format,strtotime($event->registration_last_date_time)) : '')"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.registrationLastTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="registration_last_time" fieldId="registration_last_time"
                                fieldRequired="true"
                                :fieldValue="(($event->registration_last_date_time) ? date( company()->date_format,strtotime($event->registration_last_date_time)) : '')" />
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('modules.events.registrationFeesAmount')" fieldName="registration_fees_amount"
                            fieldId="registration_fees_amount" fieldType="number" fieldPlaceholder="0.00" step="0.01"
                            :fieldValue="$event->registration_fees_amount" />
                    </div>
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="description" :fieldLabel="__('app.description')">
                            </x-forms.label>
                            <div id="description"> {!! $event->description !!} </div>
                            <textarea name="description" id="description-text" class="d-none"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <x-forms.datepicker fieldId="start_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.startOnDate')" fieldName="start_date"
                            :fieldValue="$event->start_date_time->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                    <div class="col-md-3 col-lg-3">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.startOnTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="start_time" fieldId="start_time"
                                fieldRequired="true"
                                :fieldValue="$event->start_date_time->format(company()->time_format)" />
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-3">
                        <x-forms.datepicker fieldId="end_date" fieldRequired="true"
                            :fieldLabel="__('modules.events.endOnDate')" fieldName="end_date"
                            :fieldValue="$event->end_date_time->format(company()->date_format)"
                            :fieldPlaceholder="__('placeholders.date')" />
                    </div>

                    <div class="col-md-3 col-lg-3">
                        <div class="bootstrap-timepicker timepicker">
                            <x-forms.text :fieldLabel="__('modules.events.endOnTime')"
                                :fieldPlaceholder="__('placeholders.hours')" fieldName="end_time" fieldId="end_time"
                                fieldRequired="true"
                                :fieldValue="$event->end_date_time->format(company()->time_format)" />
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.noOfSeatsForGuests')" fieldName="no_of_seats_for_guests"
                            fieldId="no_of_seats_for_guests"  :fieldValue="$event->no_of_seats_for_guests" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.guestSeatStart')" fieldName="guest_seat_start"
                            fieldId="guest_seat_start"  :fieldValue="$event->guest_seat_start" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.guestSeatEnd')" fieldName="guest_seat_end"
                            fieldId="guest_seat_end"  :fieldValue="$event->guest_seat_end" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.noOfSeatsForParticipants')" fieldName="no_of_seats_for_participants"
                            fieldId="no_of_seats_for_participants"  :fieldValue="$event->no_of_seats_for_participants" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.participantsSeatStart')" fieldName="participants_seat_start"
                            fieldId="participants_seat_start"  :fieldValue="$event->participants_seat_start" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.participantsSeatEnd')" fieldName="participants_seat_end"
                            fieldId="participants_seat_end"  :fieldValue="$event->participants_seat_end" fieldPlaceholder="eg: 5" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.maximumParticipants')" fieldName="maximum_participants"
                            fieldId="maximum_participants" :fieldValue="$event->maximum_participants" fieldPlaceholder="eg: 5" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="__('modules.events.maximumParticipantsPerUser')" fieldName="maximum_participants_per_user"
                            fieldId="maximum_participants_per_user" :fieldValue="$event->maximum_participants_per_user" fieldPlaceholder="eg: 10" />
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="__('modules.events.location')" fieldName="location"
                            fieldRequired="false" fieldId="location" :fieldValue="$event->location" fieldPlaceholder=" Location" />
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
                    <div class="col-lg-4 col-md-6 mt-2">
                        <img src="{{ $event->banner_url }}" alt="{{ $event->name }}" width="100">

                    </div>
                    <div class="col-lg-4 col-md-6 mt-2">
                        <img src="{{ $event->icon_url }}" alt="{{ $event->name }}" width="100">

                    </div>
                    <div class="col-lg-4 col-md-6 mt-2">
                        <a href="{{ $event->brocher_url }}" target="_blank" rel="noopener noreferrer">
                            View Brochure (PDF)
                        </a>
                    </div>
                    <div class="col-lg-12 d-none">
                        <x-forms.file-multiple class="mr-0" :fieldLabel="__('app.menu.addFile')"
                            fieldName="file" fieldId="file-upload-dropzone" />
                    </div>

                </div>
                <x-form-actions>
                    <x-forms.button-primary id="save-event-form" class="mr-3" icon="check">@lang('app.update')</x-forms.button-primary>
                    <x-forms.button-cancel :link="route('events.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>
<script src="{{ asset('vendor/jquery/bootstrap-colorpicker.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.select-picker').selectpicker('refresh');
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
                var ids = "{{ $event->id }}";
                // alert($ids);
                formData.append('eventId', ids);
                $.easyBlockUI();
            });
            eventDropzone.on('uploadprogress', function() {
                $.easyBlockUI();
            });
            eventDropzone.on('queuecomplete', function() {
                var msgs = "@lang('messages.recordSaved')";
                window.location.href = "{{ route('events.index') }}"
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

            $('#add-file').click(function() {
            $(this).addClass('d-none');
            $('#event-file').removeClass('d-none');
            $('#no-files').addClass('d-none');
            });

            $('#cancel-file').click(function() {
                $('#event-file').toggleClass('d-none');
                $('#add-file').toggleClass('d-none');
                $('#no-files').toggleClass('d-none');
            });

        $('body').on('change', '#employee_department', function () {

            let departmentIds = $(this).val();
            if (departmentIds === '' || departmentIds.length === 0) {
                departmentIds = 0;
            }
            let userId = '{{ $event->attendee->pluck("user.id")->implode(",") }}';

            let url = "{{ route('departments.members', ':id') }}?userId="+userId;
            url = url.replace(':id', departmentIds);

            $.easyAjax({
                url: url,
                type: "GET",
                container: '#save-project-data-form',
                blockUI: true,
                redirect: true,
                success: function (data) {
                    $('#selectAssignee').html(data.data);
                    $('#selectAssignee').selectpicker('refresh');
                }
            })
        });

            $('body').on('click', '.delete-file', function() {
                var id = $(this).data('row-id');
                Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.recoverRecord')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('event-files.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    $('#event-file-list').html(response.view);
                                }
                            }
                        });
                    }
                });
            });

        $('#send_reminder').change(function() {
            $('.send_reminder_div').toggleClass('d-none');
        })

        $('#start_time, #end_time, #registration_last_time').timepicker({
            @if (company()->time_format == 'H:i')
                showMeridian: false,
            @endif
        });

        $('#colorpicker').colorpicker({
            "color": "{{ $event->label_color }}"
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

        // quillImageLoad('#description');
        const atValues = @json($userData);

        quillMention(atValues, '#description');

        const dp1 = datepicker('#start_date', {
            position: 'bl',
            dateSelected: new Date("{{ str_replace('-', '/', $event->start_date_time) }}"),
            onSelect: (instance, date) => {
                if (typeof dp2.dateSelected !== 'undefined' && dp2.dateSelected.getTime() < date
                    .getTime()) {
                    dp2.setDate(date, true)
                }
                if (typeof dp2.dateSelected === 'undefined') {
                    dp2.setDate(date, true)
                }
                dp2.setMin(date);
            },
            ...datepickerConfig
        });

        const dp2 = datepicker('#end_date', {
            position: 'bl',
            dateSelected: new Date("{{ str_replace('-', '/', $event->end_date_time) }}"),
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
            console.log(mention_user_id);

            $('#mentionUserId').val(mention_user_id.join(','));

            const url = "{{ route('events.update', $event->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-event-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-event-form",
                data: $('#save-event-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        if(eventDropzone.getQueuedFiles().length > 0) {
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

        init(RIGHT_MODAL);
    });
</script>
