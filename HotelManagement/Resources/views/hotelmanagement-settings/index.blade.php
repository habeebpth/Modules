@extends('layouts.app')

@section('content')

@php
$addLeadAgentPermission = user()->permission('add_lead_agent');
$addLeadSourcesPermission = user()->permission('add_lead_sources');
$addLeadCategoryPermission = user()->permission('add_lead_category');
@endphp

    <!-- SETTINGS START -->
    <div class="w-100 d-flex">
        <x-setting-sidebar :activeMenu="$activeSettingMenu" />
        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active floor" href="{{ route('hotelmanagement-settings.index') }}"
                                role="tab" aria-controls="nav-floor" aria-selected="true">@lang('app.menu.floor')
                            </a>
                            <a class="nav-item nav-link f-15 roomtypes" href="{{ route('hotelmanagement-settings.index') }}?tab=roomtypes"
                            role="tab" aria-controls="nav-roomtypes" aria-selected="true">@lang('app.menu.roomtypes')
                        </a>
                        <a class="nav-item nav-link f-15 facilities" href="{{ route('hotelmanagement-settings.index') }}?tab=facilities"
                        role="tab" aria-controls="nav-facilities" aria-selected="true">@lang('app.menu.facilities')
                         </a>
                         <a class="nav-item nav-link f-15 services" href="{{ route('hotelmanagement-settings.index') }}?tab=services"
                        role="tab" aria-controls="nav-services" aria-selected="true">@lang('app.menu.services')
                        </a>
                        <a class="nav-item nav-link f-15 hmbookingsource" href="{{ route('hotelmanagement-settings.index') }}?tab=hmbookingsource"
                        role="tab" aria-controls="nav-hmbookingsource" aria-selected="true">@lang('app.menu.hmbookingsource')
                        </a>
                        <a class="nav-item nav-link f-15 bookingtype" href="{{ route('hotelmanagement-settings.index') }}?tab=bookingtype"
                        role="tab" aria-controls="nav-bookingtype" aria-selected="true">@lang('app.menu.bookingtype')
                        </a>
                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">
                    <div class="col-md-12 mb-2">
                            <x-forms.button-primary icon="plus" id="addFloor" class="floor-btn mb-2 actionBtn">
                                @lang('app.addNewFloor')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addRoomtype" class="roomtypes-btn mb-2  d-none actionBtn">
                                @lang('app.addRoomtype')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addHMFacilities" class="facilities-btn mb-2  d-none actionBtn">
                                @lang('app.addNewFacilities')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addHMService" class="services-btn mb-2  d-none actionBtn">
                                @lang('app.addservice')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addHmbookingsource" class="hmbookingsource-btn mb-2  d-none actionBtn">
                                @lang('app.addNewhmbookingsource')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addBType" class="bookingtype-btn mb-2  d-none actionBtn">
                                @lang('app.addNewbookingtype')
                            </x-forms.button-primary>
                    </div>
                </div>
            </x-slot>

            {{-- include tabs here --}}
            @include($view)

        </x-setting-card>
    </div>
    <!-- SETTINGS END -->

@endsection

@push('scripts')
    <script>
        /* MENU SCRIPTS */
        /* manage menu active class */
        $('.nav-item').removeClass('active');
        const activeTab = "{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

       $("body").on("click", "#editSettings .nav a", function(event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function(response) {
                    if (response.status == "success") {
                        showBtn(response.activeTab);
                        $('#nav-tabContent').html(response.html);
                        init('#nav-tabContent');
                    }
                }
            });
        });

        function showBtn(activeTab) {
            $('.actionBtn').addClass('d-none');
            $('.' + activeTab + '-btn').removeClass('d-none');
        }

        showBtn(activeTab);
        /* MENU SCRIPTS */

        $(document).on('show.bs.dropdown', '.table-responsive', function() {
            $('.table-responsive').css( "overflow", "inherit" );
        });


        /* LEAD SOURCE SCRIPTS */
        /* open add source modal */
        $('body').on('click', '#addFloor', function() {
            var url = "{{ route('hotelmanagement-floor-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '#addRoomtype', function() {
            var url = "{{ route('hm-roomtypes-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '#addHMService', function() {
            var url = "{{ route('hm-services-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '#addHMFacilities', function() {
            var url = "{{ route('hm-facilities-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '#addHmbookingsource', function() {
            var url = "{{ route('hmbookingsource-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '#addBType', function() {
            var url = "{{ route('hmbookingtype-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* open edit source modal */
        $('body').on('click', '.edit-floor', function() {
            var floorId = $(this).data('floor-id');
            var url = "{{ route('hotelmanagement-floor-settings.edit', ':id ') }}";
            url = url.replace(':id', floorId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.edit-roomtype', function() {
            var roomtypeId = $(this).data('roomtype-id');
            var url = "{{ route('hm-roomtypes-settings.edit', ':id ') }}";
            url = url.replace(':id', roomtypeId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-service', function() {
            var servicesId = $(this).data('service-id');
            var url = "{{ route('hm-services-settings.edit', ':id ') }}";
            url = url.replace(':id', servicesId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-facilities', function() {
            var facilitiesId = $(this).data('facilities-id');
            var url = "{{ route('hm-facilities-settings.edit', ':id ') }}";
            url = url.replace(':id', facilitiesId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-hmbookingsource', function() {
            var hmbookingsourceId = $(this).data('hmbookingsource-id');
            var url = "{{ route('hmbookingsource-settings.edit', ':id ') }}";
            url = url.replace(':id', hmbookingsourceId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-bookingtype', function() {
            var bookingtypeId = $(this).data('bookingtype-id');
            var url = "{{ route('hmbookingtype-settings.edit', ':id ') }}";
            url = url.replace(':id', bookingtypeId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* delete source */
        $('body').on('click', '.delete-floor', function() {
            var id = $(this).data('floor-id');
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

                    var url = "{{ route('hotelmanagement-floor-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });


        $('body').on('click', '.delete-roomtype', function() {
            var id = $(this).data('roomtype-id');
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

                    var url = "{{ route('hm-roomtypes-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });
        $('body').on('click', '.delete-service', function() {
            var id = $(this).data('service-id');
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

                    var url = "{{ route('hm-services-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.delete-facilities', function() {
            var id = $(this).data('facilities-id');
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

                    var url = "{{ route('hm-facilities-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });


        $('body').on('click', '.delete-hmbookingsource', function() {
            var id = $(this).data('hmbookingsource-id');
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

                    var url = "{{ route('hmbookingsource-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });
        $('body').on('click', '.delete-bookingtype', function() {
            var id = $(this).data('bookingtype-id');
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

                    var url = "{{ route('hmbookingtype-settings.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                $('.row'+id).fadeOut();
                            }
                        }
                    });
                }
            });
        });

    </script>
@endpush
