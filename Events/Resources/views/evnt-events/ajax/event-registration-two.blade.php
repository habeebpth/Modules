
<!-- ROW START -->

<div class="row pb-5">
    <div class="col-lg-12 col-md-12 mb-4 mb-xl-0 mb-lg-4">
        <form action="" id="filter-form">

            <div class="d-flex my-3 d-lg-flex d-md-flex d-block flex-wrap filter-box bg-white client-list-filter">
                <!-- STATUS START -->
                <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
                    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
                    <div class="select-status d-flex">
                        <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                            id="datatableRange" placeholder="@lang('placeholders.dateRange')">
                    </div>
                </div>
                <div class="select-box py-2 px-0 mr-3">
                    <select class="form-control select-picker" name="district_id" id="district_id">
                        <option value="all">@lang('app.District')</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="select-box py-2 px-0 mr-3">
                    <select class="form-control select-picker" name="panchayat_id" id="panchayat_id">
                        <option value="all">@lang('app.Panchayat')</option>
                        @foreach ($panchayath as $panchayat)
                            <option value="{{ $panchayat->id }}">{{ $panchayat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="select-box py-2 px-0 mr-3">
                    <select class="form-control select-picker" name="gender" id="gender">
                        <option value="all">@lang('app.gender')</option>
                        <option value="male">@lang('app.male')</option>
                        <option value="female">@lang('app.female')</option>
                        {{-- <option value="others">@lang('app.others')</option> --}}
                    </select>
                </div>
                <!-- STATUS END -->

                <!-- SEARCH BY TASK START -->
                {{-- <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">

                    <div class="input-group bg-grey rounded">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-additional-grey">
                                <i class="fa fa-search f-13 text-dark-grey"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control f-14 p-1 height-35 border" id="search-text-field"
                            placeholder="@lang('app.startTyping')">
                    </div>
                </div> --}}
                <!-- SEARCH BY TASK END -->

                <!-- RESET START -->
                <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
                    <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                        @lang('app.clearFilters')
                    </x-forms.button-secondary>
                </div>
                <!-- RESET END -->
            </div>
        </form>
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">
            <div id="table-actions" class="d-flex align-items-center">
                <x-forms.link-primary :link="route('event.event-registration.create', ['id' => $event->id])"
                    class="mr-3 openRightModal float-left"
                    icon="plus">
                    @lang('app.menu.AddEventRegistration')
                </x-forms.link-primary>

            </div>
            <x-datatable.actions>
                <div class="select-status mr-3 pl-3">
                    <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                        <option value="">@lang('app.selectAction')</option>
                        <option value="delete">@lang('app.delete')</option>
                    </select>
                </div>
            </x-datatable.actions>
        </div>
        <!-- Add Task Export Buttons End -->

        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

        </div>
        <!-- Task Box End -->
    </div>
</div>
@push('scripts')
@include('sections.datatable_js')

<script>
    $('#event-registration-table').on('preXhr.dt', function(e, settings, data) {
        var dateRangePicker = $('#datatableRange').data('daterangepicker');
        var startDate = $('#datatableRange').val();
        var EventId = "{{ $event->id }}";
        data['EventId'] = EventId;
        const districtid = $('#district_id').val();
        data['districtid'] = districtid;
        const panchayatid = $('#panchayat_id').val();
        data['panchayatid'] = panchayatid;
        const gender = $('#gender').val();
        data['gender'] = gender;
        if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }
            data['startDate'] = startDate;
            data['endDate'] = endDate;
    });

    const showTable = () => {
        window.LaravelDataTables["event-registration-table"].draw(false);
    }
    $('#district_id, #gender, #panchayat_id').on('change keyup',
            function () {
                if ($('#district_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                }else if ($('#gender').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                }else if ($('#panchayat_id').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                showTable();
            });
            $('#search-text-field').on('keyup', function() {
        if ($('#search-text-field').val() != "") {
            $('#reset-filters').removeClass('d-none');
            showTable();
        }
    });

    $('#reset-filters,#reset-filters-2').click(function() {
        $('#filter-form')[0].reset();
        $('#filter-form #status').val('not finished');
        $('#filter-form .select-picker').selectpicker("refresh");
        $('#reset-filters').addClass('d-none');
        showTable();
    });
    $('body').on('click', '.delete-table-row-event-registration', function() {
        var id = $(this).data('id');
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
                const baseUrl = "{{ url('/') }}";
                var url = "{{ route('event-registration.destroy', ':id')  }}";
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
                            window.location.href = `${baseUrl}/account/events/${response.event_id}?tab=event-registration`;
                        }
                    }
                });
            }
        });
    });

</script>
@endpush
