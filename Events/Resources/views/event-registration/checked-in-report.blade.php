@extends('layouts.app')

@push('datatable-styles')
  @include('sections.datatable_css')
@endpush

@section('filter-section')
  <x-filters.filter-box>

    <!-- DATE START -->
    <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
      <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.date')</p>
      <div class="select-status d-flex">
        <input type="text"
          class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
          id="datatableRange" placeholder="@lang('placeholders.dateRange')">
      </div>
    </div>
    <!-- DATE END -->

    <!-- SEARCH BY EVENT START -->
    <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
      <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.event')</p>
      <div class="select-status">
        <select class="form-control select-picker" name="EventId" id="EventId" data-live-search="true" data-size="8">
          <option value="all">@lang('app.all')</option>
          @foreach ($events as $event)
            <option value="{{ $event->id }}">{{ $event->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <!-- SEARCH BY EVENT END -->
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
    <!-- RESET START -->
    <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
      <x-forms.button-secondary class="btn-xs " id="reset-filters" icon="times-circle">
        @lang('app.clearFilters')
      </x-forms.button-secondary>
    </div>
    <!-- RESET END -->

    <!-- MORE FILTERS START -->
    <x-filters.more-filter-box>
      <!-- Add more filters here if needed -->
    </x-filters.more-filter-box>
    <!-- MORE FILTERS END -->
  </x-filters.filter-box>
@endsection

@section('content')
  <!-- CONTENT WRAPPER START -->
  <div class="content-wrapper">
    <!-- Add Task Export Buttons End -->
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
        </div>
    </div>

    <!-- Task Box Start -->
    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">

      {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}

    </div>
    <!-- Task Box End -->
  </div>
  <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
  @include('sections.datatable_js')

  <script>
    $(function() {
        var today = moment().format('{{ company()->moment_date_format }}');
            $('#datatableRange').daterangepicker({
                startDate: today,
                endDate: today,
                autoUpdateInput: true,
                locale: {
                    format: '{{ company()->moment_date_format }}'
                }
            });

            $('#datatableRange').val(today + ' - ' + today);

      $('#checked-in-participants-table').on('preXhr.dt', function(e, settings, data) {
        var dateRangePicker = $('#datatableRange').data('daterangepicker');
        var startDate = null;
        var endDate = null;

        if (dateRangePicker) {
          startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
          endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
        }

        var searchText = $('#search-text-field').val();
        data['searchText'] = searchText;
        data['startDate'] = startDate;
        data['endDate'] = endDate;
      });

      window.LaravelDataTables["checked-in-participants-table"].draw();


      $('#datatableRange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('{{ company()->moment_date_format }}') +
          ' @lang('app.to') ' + picker.endDate.format('{{ company()->moment_date_format }}'));
        window.LaravelDataTables["checked-in-participants-table"].draw();
      });

      $('#datatableRange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        window.LaravelDataTables["checked-in-participants-table"].draw();
      });

      $('#EventId').on('change', function() {
        window.LaravelDataTables["checked-in-participants-table"].draw();
      });

      $('#search-text-field').on('input', function() {
        window.LaravelDataTables["checked-in-participants-table"].draw();
      })

      $('#reset-filters').click(function() {
        $('#datatableRange').val('');
        $('#search-text-field').val('');
        $('#EventId').val('all').selectpicker('refresh');
        window.LaravelDataTables["checked-in-participants-table"].draw();
      });
    });
  </script>
@endpush
