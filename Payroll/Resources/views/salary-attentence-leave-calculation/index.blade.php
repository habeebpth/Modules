@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <!-- DATE START -->
        {{-- <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.duration')</p>
            <div class="select-status d-flex">
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange" placeholder="@lang('placeholders.dateRange')">
            </div>
        </div> --}}
        <!-- DATE END -->

        <div class="select-box d-flex py-2 pr-lg-3 pr-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.select') @lang('app.year')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="year" id="year">
                    @for ($i = $year; $i >= $year - 4; $i--)
                        <option @if ($i == $year) selected @endif value="{{ $i }}">
                            {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-3 px-md-3 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center" id="select-label">@lang('app.select')
                @lang('app.month')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="month" id="month">
                    @for ($m = 1; $m <= 12; $m++)
                        <option @if ($m == $month) selected @endif value="{{ sprintf('%02d', $m) }}">
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

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
            <x-forms.button-secondary class="btn-xs " id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

        <!-- MORE FILTERS START -->
        <x-filters.more-filter-box>

        </x-filters.more-filter-box>
        <!-- MORE FILTERS END -->
    </x-filters.filter-box>
@endsection


@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
                {{-- <x-forms.button-primary icon="plus" id="genarate-salarycalculation"
                    class="float-left mr-3 accounttypes-btn mb-2 actionBtn">
                    @lang('app.GenarateSalaryCalculation')
                </x-forms.button-primary> --}}
                <x-forms.link-secondary :link="route('salary-calculation.import')" class="mr-3 openRightModal mb-2 mb-lg-0 d-none d-lg-block"
                                                icon="file-upload">
                            @lang('app.importExcel')
                        </x-forms.link-secondary>
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
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        // $('body').on('click', '#genarate-salarycalculation', function() {
        //     var url = "{{ route('salary-calculation.create') }}";
        //     $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        //     $.ajaxModal(MODAL_LG, url);
        // });
        $('#salary-attentence-leave-calculation-table').on('preXhr.dt', function(e, settings, data) {
            //     var dateRangePicker = $('#datatableRange').data('daterangepicker');
            //     var startDate = $('#datatableRange').val();
            //     if (startDate == '') {
            //             startDate = null;
            //             endDate = null;
            //         } else {
            //             startDate = dateRangePicker.startDate.format('{{ company()->moment_date_format }}');
            //             endDate = dateRangePicker.endDate.format('{{ company()->moment_date_format }}');
            //         }
            // var leadID = $('#lead_id_filter').val();
            // if (!leadID) {
            //     leadID = 0;
            // }
            var searchText = $('#search-text-field').val();
            var month = $('#month').val();

            var year = $('#year').val();
            // var status = $('#status').val();
            // data['leadID'] = leadID;
            data['searchText'] = searchText;
            data['month'] = month;

            data['year'] = year;
            // data['startDate'] = startDate;
            // data['endDate'] = endDate;
            // data['status'] = status;
        });

        const showTable = () => {
            window.LaravelDataTables["salary-attentence-leave-calculation-table"].draw(true);
        }
        $(' #year, #month')
            .on('change keyup',
                function() {
                    showTable();

                });
        // $(' #lead_id_filter, #status')
        //         .on('change keyup',
        //             function() {
        //                if ($('#lead_id_filter').val() != "all") {
        //                     $('#reset-filters').removeClass('d-none');
        //                     showTable();
        //                 } else if ($('#status').val() != "all") {
        //                     $('#reset-filters').removeClass('d-none');
        //                     showTable();
        //                 } else {
        //                     $('#reset-filters').addClass('d-none');
        //                     showTable();
        //                 }
        //             });
        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });
        $('#reset-filters,#reset-filters-2').click(function() {
            $('#filter-form')[0].reset();

            $('.filter-box #status').val('not finished');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });



    </script>
@endpush
