@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <!-- CLIENT START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.Guesttype')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="guest_type" id="guest_type" data-live-search="true" data-size="8">
                    <option value="">@lang('app.all')</option>
                    @foreach ($guesttypes as $guesttype)
                    <option value="{{ $guesttype->id }}">{{ $guesttype->name }}</option>
                @endforeach
                </select>
            </div>
        </div>
        <div class="select-box d-flex py-2 px-lg-2 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('modules.guests.country')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="country" id="country" data-live-search="true" data-size="8">
                    <option value="">@lang('app.all')</option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
                </select>
            </div>
        </div>
        <div class="select-box d-flex py-2 px-lg-2 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.Race')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="race_id" id="race_id" data-live-search="true" data-size="8">
                    <option value="">@lang('app.all')</option>
                    @foreach ($races as $race)
                    <option value="{{ $race->id }}">{{ $race->name }}</option>
                @endforeach
                </select>
            </div>
        </div>

        <!-- CLIENT END -->




        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                           placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>

@endsection

@php
    $addDwcPermission = user()->permission('add_dwc');
    $editDwcPermission = user()->permission('view_dwc');
    $deleteDwcPermission = user()->permission('delete_dwc');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
                    @if($addDwcPermission == 'all')
                        <x-forms.link-primary :link="route('guests.create')" class="mr-3 openRightModal" icon="plus">
                            Add Guest
                        </x-forms.link-primary>
                        <x-forms.link-secondary :link="route('guests.import')" class="mr-3 openRightModal mb-2 mb-lg-0 d-none d-lg-block"
                                                icon="file-upload">
                            @lang('app.importExcel')
                        </x-forms.link-secondary>
                    @endif
            </div>

            <x-datatable.actions>
                <div class="select-status mr-3 pl-3">
                    <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                        <option value="">@lang('app.selectAction')</option>
                        <option value="change-status">@lang('modules.tasks.changeStatus')</option>
                        <option value="delete">@lang('app.delete')</option>
                    </select>
                </div>
                <div class="select-status mr-3 d-none quick-action-field" id="change-status-action">
                    <select name="status" class="form-control select-picker">
                        <option value="deactive">@lang('app.inactive')</option>
                        <option value="active">@lang('app.active')</option>
                    </select>
                </div>
            </x-datatable.actions>

        </div>
        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white table-responsive">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>

        var startDate = null;
        var endDate = null;
        var lastStartDate = null;
        var lastEndDate = null;

        @if(request('startDate') != '' && request('endDate') != '' )
            startDate = '{{ request("startDate") }}';
        endDate = '{{ request("endDate") }}';
        @endif

            @if(request('lastStartDate') !=='' && request('lastEndDate') !=='' )
            lastStartDate = '{{ request("lastStartDate") }}';
        lastEndDate = '{{ request("lastEndDate") }}';
        @endif

        $('#guests-table').on('preXhr.dt', function (e, settings, data) {
            const status = $('#status').val();
            const employee = $('#employee').val();
            const role = $('#role').val();
            const gender = $('#gender').val();
            const skill = $('#skill').val();
            const designation = $('#designation').val();
            const department = $('#department').val();
            const employmentType = $('#employmentType').val();
            const searchText = $('#search-text-field').val();
            var GuesttypeId = $('#guest_type').val();
            var countryId = $('#country').val();
            var raceId = $('#race_id').val();
            if (!GuesttypeId) {
                GuesttypeId = 0;
            }
            if (!raceId) {
                raceId = 0;
            }
            if (!countryId) {
                countryId = 0;
            }
            data['status'] = status;
            data['GuesttypeId'] = GuesttypeId;
            data['countryId'] = countryId;
            data['raceId'] = raceId;
            data['role'] = role;
            data['gender'] = gender;
            data['skill'] = skill;
            data['designation'] = designation;
            data['department'] = department;
            data['employmentType'] = employmentType;
            data['searchText'] = searchText;

            /* If any of these following filters are applied, then dashboard conditions will not work  */
            if (status == "all" || employee == "all" || role == "all" || designation == "all" || searchText == "") {
                data['startDate'] = startDate;
                data['endDate'] = endDate;
                data['lastStartDate'] = lastStartDate;
                data['lastEndDate'] = lastEndDate;
            }

        });

        const showTable = () => {
            window.LaravelDataTables["guests-table"].draw(false);
        }

        $('#guest_type, #status, #country, #race_id, #skill, #designation, #department, #employmentType').on('change keyup',
            function () {
                if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#guest_type').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#country').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#race_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#designation').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#department').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                }else if ($('#employmentType').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                showTable();
            });

        $('#search-text-field').on('keyup', function () {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters, #reset-filters-2').click(function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });


        $('#quick-action-type').change(function () {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

        $('#quick-action-apply').click(function () {
            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
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
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });

        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('user-id');
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
                    var url = "{{ route('guests.destroy', ':id') }}";
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
                        success: function (response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        const applyQuickAction = () => {
            var rowdIds = $("#guests-table input:checkbox:checked").map(function () {
                return $(this).val();
            }).get();

            var url = "{{ route('employees.apply_quick_action') }}?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                        $('#quick-action-form').hide();
                    }
                }
            })
        };


        $('body').on('change', '.assign_role', function () {
            var id = $(this).data('user-id');
            var role = $(this).val();
            var token = "{{ csrf_token() }}";

            if (typeof id !== 'undefined') {
                $.easyAjax({
                    url: "{{ route('employees.assign_role') }}",
                    type: "POST",
                    blockUI: true,
                    container: '#guests-table',
                    data: {
                        role: role,
                        userId: id,
                        _token: token
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.LaravelDataTables["guests-table"].draw(false);
                        }
                    }
                })
            }

        });

        $('#designation-setting').click(function () {
            const url = "{{ route('designations.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        $('.department-setting').click(function () {
            const url = "{{ route('departments.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
@endpush
