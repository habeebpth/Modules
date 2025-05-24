@extends('layouts.app')

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <div class="d-flex justify-content-between px-4 py-3">
                <div>
                    <h5 class="mb-0 f-18 text-capitalize font-weight-bold">
                        @lang('synktime::app.menu.SyncingHistory') @lang('app.details')
                    </h5>
                    <p class="mb-0 text-lightest">@lang('app.id'): #{{ $synkingHistory->id }}</p>
                </div>
                <div>
                    <a href="{{ route('synking-history.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-chevron-left mr-1"></i> @lang('app.back')
                    </a>
                </div>
            </div>

            <div class="d-flex px-4 py-3 border-top-grey">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">@lang('app.created_at')</p>
                                <p class="f-14 font-weight-bold mb-3">{{ \Carbon\Carbon::parse($synkingHistory->created_at_ist)->format('d M Y, h:i A') }}</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">@lang('synktime::app.sync_type')</p>
                                <p class="f-14 font-weight-bold mb-3">
                                    @if($synkingHistory->sync_type == 'department')
                                        <span class="badge badge-primary">@lang('synktime::app.department_sync')</span>
                                    @elseif($synkingHistory->sync_type == 'area')
                                        <span class="badge badge-success">@lang('synktime::app.area_sync')</span>
                                    @elseif($synkingHistory->sync_type == 'employee')
                                        <span class="badge badge-info">@lang('synktime::app.employee_sync')</span>
                                    @else
                                        <span class="badge badge-secondary">@lang('synktime::app.attendance_sync')</span>
                                    @endif
                                </p>
                            </div>

                            @if($synkingHistory->sync_type && in_array($synkingHistory->sync_type, ['department', 'area', 'employee']))
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">@lang('synktime::app.total_synced')</p>
                                    <p class="f-14 font-weight-bold mb-3">{{ $synkingHistory->total_synced ?? 0 }}</p>
                                </div>
                            @endif

                            @if($synkingHistory->from_date)
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">@lang('synktime::app.from_date')</p>
                                    <p class="f-14 font-weight-bold mb-3">{{ \Carbon\Carbon::parse($synkingHistory->from_date)->format('d M Y') }}</p>
                                </div>
                            @endif

                            @if($synkingHistory->to_date)
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">@lang('synktime::app.to_date')</p>
                                    <p class="f-14 font-weight-bold mb-3">{{ \Carbon\Carbon::parse($synkingHistory->to_date)->format('d M Y') }}</p>
                                </div>
                            @endif

                            @if($synkingHistory->employee_id && $synkingHistory->employee)
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">@lang('app.employee')</p>
                                    <p class="f-14 font-weight-bold mb-3">
                                        <a href="{{ route('employees.show', $synkingHistory->employee_id) }}">
                                            {{ $synkingHistory->employee->name }}
                                        </a>
                                    </p>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <p class="text-muted mb-1">@lang('app.createdBy')</p>
                                <p class="f-14 font-weight-bold mb-3">
                                    <a href="{{ route('employees.show', $synkingHistory->created_by) }}">
                                        {{ $synkingHistory->createdBy->name }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection
