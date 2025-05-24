@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
<x-filters.filter-box>
    <!-- DATE RANGE START -->
    <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
        <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
        <div class="select-status d-flex">
            <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                id="datatableRange" placeholder="@lang('placeholders.dateRange')">
        </div>
    </div>
    <!-- DATE RANGE END -->

    <!-- STATUS FILTER START -->
    <div class="select-box py-2 px-lg-2 px-md-2 px-0 mr-3">
        <select class="form-control select-picker" name="status" id="status" data-live-search="true">
            <option value="all">@lang('All Status')</option>
            <option value="draft">@lang('Draft')</option>
            <option value="posted">@lang('Posted')</option>
            <option value="reversed">@lang('Reversed')</option>
        </select>
    </div>
    <!-- STATUS FILTER END -->

    <!-- SEARCH START -->
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
    <!-- SEARCH END -->

    <!-- RESET START -->
    <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
        <x-forms.button-secondary class="btn-xs" id="reset-filters" icon="times-circle">
            @lang('app.clearFilters')
        </x-forms.button-secondary>
    </div>
    <!-- RESET END -->
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.journals.create')"
                class="mr-3 openRightModal float-left" icon="plus">
                @lang('Create Journal Entry')
            </x-forms.link-primary>
        </div>

        <x-datatable.actions>
            <div class="select-status mr-3 pl-3">
                <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                    <option value="">@lang('app.selectAction')</option>
                    <option value="post">@lang('Post Entries')</option>
                    <option value="delete">@lang('app.delete')</option>
                </select>
            </div>
        </x-datatable.actions>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
    </div>
</div>
@endsection

@push('scripts')
@include('sections.datatable_js')

<script>
$('#journals-table').on('preXhr.dt', function(e, settings, data) {
    var dateRangePicker = $('#datatableRange').data('daterangepicker');
    var startDate = $('#datatableRange').val();

    if (startDate == '') {
        startDate = null;
        endDate = null;
    } else {
        startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
        endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
    }

    var status = $('#status').val();
    var searchText = $('#search-text-field').val();

    data['startDate'] = startDate;
    data['endDate'] = endDate;
    data['status'] = status;
    data['searchText'] = searchText;
});

const showTable = () => {
    window.LaravelDataTables["journals-table"].draw(false);
}

$('#status, #search-text-field').on('change keyup', function() {
    if ($('#status').val() != "all" || $('#search-text-field').val() != "") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else if ($('#datatableRange').val() != "") {
        $('#reset-filters').removeClass('d-none');
        showTable();
    } else {
        $('#reset-filters').addClass('d-none');
        showTable();
    }
});

$('#reset-filters').click(function() {
    $('#filter-form')[0].reset();
    $('#status').val('all');
    $('.filter-box .select-picker').selectpicker("refresh");
    $('#datatableRange').val('');
    $('#search-text-field').val('');
    $('#reset-filters').addClass('d-none');
    showTable();
});

// Post journal entry
$('body').on('click', '.post-journal', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('Are you sure you want to post this journal entry?')",
        icon: 'warning',
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "@lang('Yes, Post it!')",
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
            var url = "{{ route('accounting.journals.post', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: { '_token': token },
                success: function(response) {
                    if (response.status == "success") {
                        showTable();
                    }
                }
            });
        }
    });
});

// Reverse journal entry
$('body').on('click', '.reverse-journal', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "@lang('messages.sweetAlertTitle')",
        text: "@lang('Are you sure you want to reverse this journal entry?')",
        icon: 'warning',
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "@lang('Yes, Reverse it!')",
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
            var url = "{{ route('accounting.journals.reverse', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'POST',
                url: url,
                data: { '_token': token },
                success: function(response) {
                    if (response.status == "success") {
                        showTable();
                    }
                }
            });
        }
    });
});
</script>
@endpush
