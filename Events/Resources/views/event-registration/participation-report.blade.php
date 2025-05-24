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
                <input type="text" class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
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

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs" id="reset-filters" icon="times-circle">
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
        <!-- Add Task Export Buttons End -->

        <!-- Task Box Start -->
        <div class="row">
            {{-- Registration Stats --}}
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-registrations-card" class="bg-light border border-info" :title="__('events::events.totalRegistrations')">
                    <i class="fa fa-2x fa-file-alt text-info"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_registrations ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-reg-male-card" class="bg-light border border-success" :title="__('events::events.totalMaleRegistered')">
                    <i class="fa fa-2x fa-male text-success"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_reg_male ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-reg-female-card" class="bg-light border border-danger" :title="__('events::events.totalFemaleRegistered')">
                    <i class="fa fa-2x fa-female text-danger"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_reg_female ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-reg-kids-card" class="bg-light border border-warning" :title="__('events::events.totalKidsRegistered')">
                    <i class="fa fa-2x fa-child text-warning"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_reg_kids ?? '0' }}</span>
                </x-cards.data>
            </div>

            {{-- Checked-in Participants Stats --}}
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-participants-card" class="bg-light border border-info" :title="__('events::events.totalCheckedInParticipants')">
                    <i class="fa fa-2x fa-group text-info"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_participants ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="unique-participants-card" class="bg-light border border-primary" :title="__('events::events.uniqueCheckedInParticipants')">
                    <i class="fa fa-2x fa-user-check text-primary"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $unique_participants ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-male-card" class="bg-light border border-success" :title="__('events::events.totalCheckedInMale')">
                    <i class="fa fa-2x fa-male text-success"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_male ?? '0' }}</span>
                </x-cards.data>
        </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-female-card" class="bg-light border border-danger" :title="__('events::events.totalCheckedInFemale')">
                    <i class="fa fa-2x fa-female text-danger"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_female ?? '0' }}</span>
                </x-cards.data>
    </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-kids-card" class="bg-light border border-warning" :title="__('events::events.totalCheckedInKids')">
                    <i class="fa fa-2x fa-child text-warning"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_kids ?? '0' }}</span>
                </x-cards.data>
            </div>

            {{-- Currently Inside Stats --}}
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-participants-inside-card" class="bg-light border border-primary" :title="__('events::events.totalParticipantsInside')">
                    <i class="fa fa-2x fa-user-circle text-primary"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_inside ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-male-inside-card" class="bg-light border border-success" :title="__('events::events.totalMaleInside')">
                    <i class="fa fa-2x fa-male text-success"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_male_inside ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-female-inside-card" class="bg-light border border-danger" :title="__('events::events.totalFemaleInside')">
                    <i class="fa fa-2x fa-female text-danger"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_female_inside ?? '0' }}</span>
                </x-cards.data>
            </div>
            <div class="col-md-3 mb-4">
                <x-cards.data id="total-kids-inside-card" class="bg-light border border-warning" :title="__('events::events.totalKidsInside')">
                    <i class="fa fa-2x fa-child text-warning"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $total_kids_inside ?? '0' }}</span>
                </x-cards.data>
            </div>

            {{-- Average Time Spent --}}
            <div class="col-md-3 mb-4">
                <x-cards.data id="average-spend-time-card" class="bg-light border border-secondary" :title="__('events::events.averageTimeSpent')">
                    <i class="fa fa-2x fa-clock text-secondary"></i>
                    &nbsp;
                    <span class="h4 font-weight-bold">{{ $avg_time_spent ?? '0' }}</span>
                </x-cards.data>
            </div>
        </div>

        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->
@push('scripts')
    @include('sections.daterange_js')
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

            $('#datatableRange').on('change', function () {
                var startDate = $('#datatableRange').val().split(' - ')[0];
                var endDate = $('#datatableRange').val().split(' - ')[1];
                var events = $('#EventId').val();

                window.location.href = '{{ route('event.participation-report') }}' + '?startDate=' + startDate + '&endDate=' + endDate + '&EventId=' + events ;
            });

            $('#EventId').on('change', function () {
                var startDate = $('#datatableRange').val().split(' - ')[0];
                var endDate = $('#datatableRange').val().split(' - ')[1];
                var events = $('#EventId').val();

                window.location.href = '{{ route('event.participation-report') }}' + '?startDate=' + startDate + '&endDate=' + endDate + '&EventId=' + events ;
            })


        });
    </script>
@endpush
@endsection
