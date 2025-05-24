@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <!-- DATE RANGE -->
        <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div>
        <!-- DATE RANGE END -->

        <!-- STATUS FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" id="status">
                    <option value="all">@lang('app.all')</option>
                    <option value="draft">@lang('app.draft')</option>
                    <option value="posted">@lang('app.posted')</option>
                    <option value="voided">@lang('app.voided')</option>
                </select>
            </div>
        </div>
        <!-- STATUS FILTER END -->

        <!-- SEARCH BY ENTRY -->
        <div class="task-search d-flex py-1 px-lg-3 px-0 border-right-grey align-items-center">
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
        <!-- SEARCH BY ENTRY END -->

        <!-- RESET FILTERS -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET FILTERS END -->
    </x-filters.filter-box>
@endsection

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-grid d-lg-flex d-md-flex action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                <x-forms.link-primary :link="route('journal-entries.create')" class="mr-3 openRightModal float-left" icon="plus">
                    @lang('app.addJournalEntry')
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

        <!-- Journal Entries Table Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
        </div>
        <!-- Journal Entries Table Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $('#journal-entries-table').on('preXhr.dt', function(e, settings, data) {
            var dateRangePicker = $('#datatableRange').data('daterangepicker');
            var startDate = $('#datatableRange').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
                endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            }

            var searchText = $('#search-text-field').val();
            var status = $('#status').val();

            data['searchText'] = searchText;
            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['status'] = status;
        });

         // Post Journal Entry
         $('body').on('click', '.post-journal-entry', function() {
            var id = $(this).data('journal-entry-id');

            Swal.fire({
                title: "@lang('messages.confirmation.postJournalEntry')",
                text: "@lang('messages.confirmation.postJournalEntryText')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('app.yes')",
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
                    var url = "{{ route('journal-entries.post') }}";
                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            'id': id
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        // Void Journal Entry
        $('body').on('click', '.void-journal-entry', function() {
            var id = $(this).data('journal-entry-id');

            Swal.fire({
                title: "@lang('messages.confirmation.voidJournalEntry')",
                text: "@lang('messages.confirmation.voidJournalEntryText')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('app.yes')",
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
                    var url = "{{ route('journal-entries.void') }}";
                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            'id': id
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        const showTable = () => {
            window.LaravelDataTables["journal-entries-table"].draw(true);
        }

        $('#search-text-field, #status, #datatableRange').on('change keyup', function() {
            if ($('#search-text-field').val() != "" || $('#status').val() != "all" || $('#datatableRange').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else {
                $('#reset-filters').addClass('d-none');
                showTable();
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box #status').val('all');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');

            // Reset datepicker
            $('#datatableRange').data('daterangepicker').setStartDate(moment().startOf('month'));
            $('#datatableRange').data('daterangepicker').setEndDate(moment().endOf('month'));
            $('#datatableRange').val('');

            showTable();
        });

        // Delete Journal Entry
        $('body').on('click', '.delete-journal-entry', function() {
            var id = $(this).data('journal-entry-id');

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
                    var url = "{{ route('journal-entries.destroy', ':id') }}";
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
                                showTable();
                            }
                        }
                    });
                }
            });
    </script>
@endpush

