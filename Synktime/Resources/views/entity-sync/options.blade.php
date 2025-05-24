@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-flex flex-column">
            <!-- Entity Sync Cards Start -->
            <div class="row">
                <!-- Department Sync Card -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <div class="card bg-white border-0 b-shadow-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h5 class="card-title mb-0">@lang('synktime::app.department_sync')</h5>
                                    <p class="text-lightest mb-0">@lang('synktime::app.sync_departments_description')</p>
                                </div>
                                <div>
                                    <i class="fa fa-building text-lightest f-21"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <x-forms.button-primary id="sync-departments" icon="sync">
                                    @lang('synktime::app.sync_departments')
                                </x-forms.button-primary>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Area Sync Card -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <div class="card bg-white border-0 b-shadow-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h5 class="card-title mb-0">@lang('synktime::app.area_sync')</h5>
                                    <p class="text-lightest mb-0">@lang('synktime::app.sync_areas_description')</p>
                                </div>
                                <div>
                                    <i class="fa fa-map-marker text-lightest f-21"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <x-forms.button-primary id="sync-areas" icon="sync">
                                    @lang('synktime::app.sync_areas')
                                </x-forms.button-primary>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Employee Sync Card -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <div class="card bg-white border-0 b-shadow-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h5 class="card-title mb-0">@lang('synktime::app.employee_sync')</h5>
                                    <p class="text-lightest mb-0">@lang('synktime::app.sync_employees_description')</p>
                                </div>
                                <div>
                                    <i class="fa fa-users text-lightest f-21"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <x-forms.button-primary id="sync-employees" icon="sync">
                                    @lang('synktime::app.sync_employees')
                                </x-forms.button-primary>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Attendance Sync Card -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
                    <div class="card bg-white border-0 b-shadow-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h5 class="card-title mb-0">@lang('synktime::app.attendance_sync')</h5>
                                    <p class="text-lightest mb-0">@lang('synktime::app.sync_attendance_description')</p>
                                </div>
                                <div>
                                    <i class="fa fa-calendar-check-o text-lightest f-21"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <x-forms.button-primary id="sync-attendance" icon="sync">
                                    @lang('synktime::app.sync_attendance')
                                </x-forms.button-primary>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-white border-0 b-shadow-4">
                        <div class="card-header bg-white border-0 text-capitalize d-flex justify-content-between p-20">
                            <h4 class="card-title mb-0">@lang('synktime::app.sync_statistics')</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <div class="bg-light-blue rounded p-4 text-center">
                                        <h2 class="f-18">@lang('synktime::app.departments')</h2>
                                        <h3 class="f-22">{{ \App\Models\Team::count() }}</h3>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <div class="bg-light-green rounded p-4 text-center">
                                        <h2 class="f-18">@lang('synktime::app.areas')</h2>
                                        <h3 class="f-22">{{ \App\Models\CompanyAddress::count() }}</h3>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <div class="bg-light-yellow rounded p-4 text-center">
                                        <h2 class="f-18">@lang('synktime::app.employees')</h2>
                                        <h3 class="f-22">{{ \App\Models\EmployeeDetails::count() }}</h3>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <div class="bg-light-grey rounded p-4 text-center">
                                        <h2 class="f-18">@lang('synktime::app.attendance_records')</h2>
                                        <h3 class="f-22">{{ \App\Models\Attendance::whereMonth('clock_in_time', now()->month)->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sync History -->
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
                <div class="d-flex justify-content-between p-3">
                    <h5 class="mb-0 f-18 text-capitalize font-weight-bold">@lang('synktime::app.recent_sync_history')</h5>
                    <div>
                        <a href="{{ route('synking-history.index') }}?show_entity_syncs=true" class="btn btn-sm btn-primary">
                            @lang('app.viewAll')
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('app.id')</th>
                                <th>@lang('app.date')</th>
                                <th>@lang('synktime::app.sync_type')</th>
                                <th>@lang('synktime::app.total_synced')</th>
                                <th>@lang('app.by')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSyncs as $sync)
                                <tr>
                                    <td>{{ $sync->id }}</td>
                                    <td>{{ $sync->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if($sync->sync_type == 'department')
                                            <span class="badge badge-primary">@lang('synktime::app.department_sync')</span>
                                        @elseif($sync->sync_type == 'area')
                                            <span class="badge badge-success">@lang('synktime::app.area_sync')</span>
                                        @elseif($sync->sync_type == 'employee')
                                            <span class="badge badge-info">@lang('synktime::app.employee_sync')</span>
                                        @elseif($sync->sync_type == 'attendance')
                                            <span class="badge badge-warning">@lang('synktime::app.attendance_sync')</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $sync->sync_type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $sync->total_synced ?? 0 }}</td>
                                    <td>
                                        @if($sync->createdBy)
                                            <a href="{{ route('employees.show', $sync->created_by) }}">
                                                {{ $sync->createdBy->name }}
                                            </a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">@lang('messages.noRecordFound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to handle sync operations
            function performSync(url, buttonId) {
                var button = $('#' + buttonId);
                button.html('<i class="fa fa-spinner fa-spin"></i> @lang("app.processing")');
                button.attr('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                            // Reload the page to show updated sync history
                            window.location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: response.message
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred while syncing. Please try again.'
                        });
                    },
                    complete: function() {
                        // Reset button state
                        if (buttonId === 'sync-departments') {
                            button.html('<i class="fa fa-sync mr-1"></i> @lang("synktime::app.sync_departments")');
                        } else if (buttonId === 'sync-areas') {
                            button.html('<i class="fa fa-sync mr-1"></i> @lang("synktime::app.sync_areas")');
                        } else if (buttonId === 'sync-employees') {
                            button.html('<i class="fa fa-sync mr-1"></i> @lang("synktime::app.sync_employees")');
                        } else if (buttonId === 'sync-attendance') {
                            button.html('<i class="fa fa-sync mr-1"></i> @lang("synktime::app.sync_attendance")');
                        }
                        button.attr('disabled', false);
                    }
                });
            }

            // Department Sync
            $('#sync-departments').click(function() {
                performSync('{{ route("entity-sync.departments") }}', 'sync-departments');
            });

            // Area Sync
            $('#sync-areas').click(function() {
                performSync('{{ route("entity-sync.areas") }}', 'sync-areas');
            });

            // Employee Sync
            $('#sync-employees').click(function() {
                performSync('{{ route("entity-sync.employees") }}', 'sync-employees');
            });

            // Attendance Sync
            $('#sync-attendance').click(function() {
                performSync('{{ route("entity-sync.attendance") }}', 'sync-attendance');
            });
        });
    </script>
@endpush
