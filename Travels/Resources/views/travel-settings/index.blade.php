@extends('layouts.app')

@section('content')


    <!-- SETTINGS START -->
    <div class="w-100 d-flex">
        <x-setting-sidebar :activeMenu="$activeSettingMenu" />
        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <nav class="tabs px-4 border-bottom-grey">
                        <div class="nav" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link f-15 active airline" href="{{ route('travel-settings.index') }}"
                                role="tab" aria-controls="nav-airline" aria-selected="true">@lang('travels::app.travels.Airline')
                            </a>
                            <a class="nav-item nav-link f-15 destination" href="{{ route('travel-settings.index') }}?tab=destination"
                            role="tab" aria-controls="nav-destination" aria-selected="true">@lang('travels::app.travels.destination')
                        </a>
                        <a class="nav-item nav-link f-15 vehicletype" href="{{ route('travel-settings.index') }}?tab=vehicletype"
                            role="tab" aria-controls="nav-vehicletype" aria-selected="true">@lang('travels::app.travels.vehicletype')
                        </a>
                        <a class="nav-item nav-link f-15 vehicle" href="{{ route('travel-settings.index') }}?tab=vehicle"
                            role="tab" aria-controls="nav-vehicle" aria-selected="true">@lang('travels::app.travels.vehicle')
                        </a>
                        </div>
                    </nav>
                </div>
            </x-slot>

            <x-slot name="buttons">
                <div class="row">
                    <div class="col-md-12 mb-2">
                            <x-forms.button-primary icon="plus" id="addairline" class="airline-btn mb-2 actionBtn">
                                @lang('travels::app.travels.addNewairline')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="adddestination" class="destination-btn mb-2  d-none actionBtn">
                                @lang('travels::app.travels.adddestination')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addvehicletype" class="vehicletype-btn mb-2  d-none actionBtn">
                                @lang('travels::app.travels.addvehicle_types')
                            </x-forms.button-primary>
                            <x-forms.button-primary icon="plus" id="addvehicle" class="vehicle-btn mb-2  d-none actionBtn">
                                @lang('travels::app.travels.addvehicle')
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
        $('body').on('click', '#addairline', function() {
            var url = "{{ route('travel-airline-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '#adddestination', function() {
            var url = "{{ route('destination-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '#addvehicletype', function() {
            var url = "{{ route('vehicletype-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '#addvehicle', function() {
            var url = "{{ route('vehicle-settings.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* open edit source modal */
        $('body').on('click', '.edit-airline', function() {
            var airlineId = $(this).data('airline-id');
            var url = "{{ route('travel-airline-settings.edit', ':id ') }}";
            url = url.replace(':id', airlineId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.edit-destination', function() {
            var destinationId = $(this).data('destination-id');
            var url = "{{ route('destination-settings.edit', ':id ') }}";
            url = url.replace(':id', destinationId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-vehicletype', function() {
            var vehicletypeId = $(this).data('vehicletype-id');
            var url = "{{ route('vehicletype-settings.edit', ':id ') }}";
            url = url.replace(':id', vehicletypeId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        $('body').on('click', '.edit-vehicle', function() {
            var vehicleId = $(this).data('vehicle-id');
            var url = "{{ route('vehicle-settings.edit', ':id ') }}";
            url = url.replace(':id', vehicleId);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
        /* delete source */
        $('body').on('click', '.delete-airline', function() {
            var id = $(this).data('airline-id');
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

                    var url = "{{ route('travel-airline-settings.destroy', ':id') }}";
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


        $('body').on('click', '.delete-destination', function() {
            var id = $(this).data('destination-id');
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

                    var url = "{{ route('destination-settings.destroy', ':id') }}";
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

        $('body').on('click', '.delete-vehicletype', function() {
            var id = $(this).data('vehicletype-id');
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

                    var url = "{{ route('vehicletype-settings.destroy', ':id') }}";
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
        $('body').on('click', '.delete-vehicle', function() {
            var id = $(this).data('vehicle-id');
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

                    var url = "{{ route('vehicle-settings.destroy', ':id') }}";
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
