@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <!-- SEARCH BY TASK START -->
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
        <!-- SEARCH BY TASK END -->

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
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-grid d-lg-flex d-md-flex action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if(!request()->has('show_entity_syncs'))
                    <x-forms.button-primary icon="plus" id="data-synk" class="mb-2">
                        @lang('synktime::app.menu.dataSynk')
                    </x-forms.button-primary>
                @else
                    <x-forms.link-primary :link="route('entity-sync.options')" icon="plus" class="mb-2">
                        @lang('app.back')
                    </x-forms.link-primary>
                @endif
            </div>
        </div>

        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            @if(isset($dataTable))
                {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover w-100" id="synking-history-table">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('app.createdBy')</th>
                                <th>@lang('synktime::app.sync_type')</th>
                                @if(request()->has('show_entity_syncs'))
                                    <th>@lang('synktime::app.total_synced')</th>
                                @else
                                    <th>@lang('app.employee')</th>
                                    <th>@lang('synktime::app.from_date')</th>
                                    <th>@lang('synktime::app.to_date')</th>
                                @endif
                                <th>@lang('app.createdAt')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($SynkingHistory as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>
                                        @if($history->createdBy)
                                            {{ $history->createdBy->name }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->sync_type == 'department')
                                            <span class="badge badge-primary">@lang('synktime::app.department_sync')</span>
                                        @elseif($history->sync_type == 'area')
                                            <span class="badge badge-success">@lang('synktime::app.area_sync')</span>
                                        @elseif($history->sync_type == 'employee')
                                            <span class="badge badge-info">@lang('synktime::app.employee_sync')</span>
                                        @else
                                            <span class="badge badge-secondary">@lang('synktime::app.attendance_sync')</span>
                                        @endif
                                    </td>

                                    @if(request()->has('show_entity_syncs'))
                                        <td>{{ $history->total_synced ?? 0 }}</td>
                                    @else
                                        <td>
                                            @if($history->employee)
                                                {{ $history->employee->name }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            @if($history->from_date)
                                                {{ \Carbon\Carbon::parse($history->from_date)->format('d M Y') }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            @if($history->to_date)
                                                {{ \Carbon\Carbon::parse($history->to_date)->format('d M Y') }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                    @endif

                                    <td>
                                        {{ $history->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td>
                                        <div class="task_view">
                                            <div class="dropdown">
                                                <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                                                    id="dropdownMenuLink-{{ $history->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-options-vertical icons"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-{{ $history->id }}" tabindex="0">
                                                    <a class="dropdown-item" href="{{ route('synking-history.show', [$history->id]) }}">
                                                        <i class="fa fa-eye mr-2"></i>
                                                        @lang('app.view')
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="shadow-none">
                                        <x-cards.no-record icon="list" :message="__('messages.noRecordFound')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>
        $('body').on('click', '#data-synk', function() {
            var url = "{{ route('synking-history.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#synking-history-table').on('preXhr.dt', function(e, settings, data) {
            var searchText = $('#search-text-field').val();
            data['searchText'] = searchText;
        });

        const showTable = () => {
            window.LaravelDataTables["synking-history-table"].draw(true);
        }

        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();

            $('#reset-filters').addClass('d-none');
            showTable();
        });

        // Initialize DataTable conditionally
        $(document).ready(function() {
            if (!window.LaravelDataTables || !window.LaravelDataTables["synking-history-table"]) {
                $('#synking-history-table').DataTable({
                    order: [[0, 'desc']],
                    language: {
                        "url": "{{ asset('vendor/datatables/'.user()->locale.'.json') }}"
                    }
                });
            }
        });
    </script>
@endpush
