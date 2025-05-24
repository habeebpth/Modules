<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
<div class="d-lg-flex">
    <div class="w-100 py-0 py-lg-3 py-md-0 ">
<!-- TAB CONTENT START -->
<x-cards.data :title="__('app.roomtype') . ' ' . __('app.details')"
otherClasses="d-flex justify-content-between align-items-center">
<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
    <div class="p-20">

        <div class="row">
            <div class="col-md-12">
                <a class="f-15 f-w-500" href="javascript:;" id="add-roomtype"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.menu.roomtype')
                </a>
            </div>
        </div>
        <x-form id="save-roomtype-data-form" class="d-none">
            <input type="hidden" name="hotel_id" value="{{ $hotels->id }}">
            <div class="row">
                <div class="col-md-12">
                    <x-forms.text :fieldLabel="__('app.roomtype')" fieldName="room_type" fieldRequired="true" fieldId="room_type"
                        :fieldPlaceholder="__('placeholders.roomtype')" />
                </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="max_occupancy" :fieldLabel="__('app.maxOccupancy')" fieldName="max_occupancy"
                        fieldRequired="false" :fieldPlaceholder="__('placeholders.max_occupancy')">
                    </x-forms.text>
                </div>
                <div class="col-md-6">
                    <x-forms.text fieldId="price_per_night" :fieldLabel="__('app.PricePerNight')" fieldName="price_per_night"
                        fieldRequired="false" :fieldPlaceholder="__('placeholders.price_per_night')">
                    </x-forms.text>
                </div>
                <div class="col-md-12">
                    <x-forms.textarea fieldId="amenities" :fieldLabel="__('app.amenities')" fieldName="amenities" fieldRequired="false"
                        :fieldPlaceholder="__('placeholders.amenities')">
                    </x-forms.textarea>
                </div>
                <div class="col-md-12">
                    <div class="w-100 justify-content-end d-flex mt-2">
                        <x-forms.button-cancel id="cancel-roomtype" class="border-0 mr-3">@lang('app.cancel')
                        </x-forms.button-cancel>
                        <x-forms.button-primary id="save-roomtype" icon="location-arrow">@lang('app.submit')
                        </x-forms.button-primary>
                    </div>
                </div>
            </div>
        </x-form>
    </div>

    <div class="d-flex flex-wrap justify-content-between p-20" id="room-type-list">
        @forelse ($hotels->roomtype as $roomtypes)
            <div class="card w-100 rounded-0 border-0 subtask mb-1">

                <div class="card-horizontal">
                    <div class="d-flex">
                        <x-forms.checkbox :fieldId="'checkbox' . $roomtypes->id" class="task-check" data-sub-task-id="{{ $roomtypes->id }}"
                            fieldLabel="" :fieldName="'checkbox' . $roomtypes->id" />
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex">

                            <p class="card-title f-14 mr-3 text-dark flex-grow-1" id="subTask">
                                <a class="view-subtask text-dark-grey" href="javascript:;"
                                    data-row-id="{{ $roomtypes->id }}">
                                    {{ $roomtypes->room_type }}
                                </a>
                            </p>

                            <div class="dropdown ml-auto subtask-action">
                                <button
                                    class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                    aria-labelledby="dropdownMenuLink" tabindex="0">
                                    {{-- <a class="dropdown-item view-subtask" href="javascript:;"
                                        data-row-id="{{ $roomtypes->id }}">@lang('app.view')</a> --}}
                                    <a class="dropdown-item edit-roomType" href="javascript:;"
                                        data-row-id="{{ $roomtypes->id }}">@lang('app.edit')</a>
                                    <a class="dropdown-item delete-subtask" data-row-id="{{ $roomtypes->id }}"
                                        href="javascript:;">@lang('app.delete')</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <x-cards.no-record :message="__('messages.noSubTaskFound')" icon="hotel" />
        @endforelse

    </div>

</div>
</x-cards.data>
</div>

</div>
<!-- TAB CONTENT END -->

<script>
    $(document).ready(function() {

        var send_approval = "{{ $hotels->approval_send }}";
        var admin = "{{ in_array('admin', user_roles()) }}";
        var employee = "{{ in_array('employee', user_roles()) }}";

        $('body').on('click', '#add-roomtype', function() {
            $(this).closest('.row').addClass('d-none');
            $('#save-roomtype-data-form').removeClass('d-none');
        });
        $('body').on('click', '#cancel-roomtype', function() {
            $('#save-roomtype-data-form').addClass('d-none');
            $('#add-roomtype').closest('.row').removeClass('d-none');
        });

        $('.select-picker').selectpicker();

        $('body').on('click', '.view-subtask', function() {
            var id = $(this).data('row-id');
            var url = "{{ route('sub-tasks.show', ':id') }}";
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#save-roomtype').click(function() {
            console.log('123');


            const url = "{{ route('hotel.roomtype.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-roomtype-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-roomtype",
                data: $('#save-roomtype-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                      if ($(RIGHT_MODAL).hasClass('in')) {
                            document.getElementById('close-task-detail').click();
                            if ($('#dwc-hotel-table').length) {
                                window.LaravelDataTables["dwc-hotel-table"].draw(false);
                            } else {
                                // window.location.href = response.redirectUrl;
                            }
                        } else {
                            window.location.reload();
                            // window.location.href = response.redirectUrl;
                        }
                    }
                }
            });
        });

        $('body').on('click', '.delete-sub-task-file', function() {
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
                    var url = "{{ route('sub-task-files.destroy', ':id') }}";
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
                                $('.subTask' + id).remove();
                            }
                        }
                    });
                }
            });
        });

    });
</script>
