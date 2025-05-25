@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div id="table-actions" class="flex-grow-1 align-items-center">
            <x-forms.button-primary class="mr-3 float-left" icon="plus"
                data-toggle="modal" data-target="#createFiscalYearModal">
                @lang('Create Fiscal Year')
            </x-forms.button-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover border-0 w-100">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Start Date')</th>
                        <th>@lang('End Date')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fiscalYears as $year)
                    <tr>
                        <td>{{ $year->name }}</td>
                        <td>{{ $year->start_date->format(company()->date_format) }}</td>
                        <td>{{ $year->end_date->format(company()->date_format) }}</td>
                        <td>
                            @if($year->is_active)
                                <span class="badge badge-success">@lang('Active')</span>
                            @else
                                <span class="badge badge-secondary">@lang('Inactive')</span>
                            @endif
                            @if($year->is_closed)
                                <span class="badge badge-danger ml-1">@lang('Closed')</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    @lang('Actions')
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">@lang('Edit')</a>
                                    @if(!$year->is_closed)
                                        <a class="dropdown-item text-danger" href="#">@lang('Close Year')</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">@lang('No fiscal years found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Fiscal Year Modal -->
<div class="modal fade" id="createFiscalYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Create Fiscal Year')</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <x-form id="fiscal-year-form">
                <div class="modal-body">
                    <x-forms.text fieldId="name" :fieldLabel="__('Name')" fieldName="name" fieldRequired="true" />

                    <x-forms.datepicker
                        fieldPlaceholder="@lang('Select Start Date')"
                        fieldId="start_date"
                        :fieldLabel="__('Start Date')"
                        fieldName="start_date"
                        fieldRequired="true" />

                    <x-forms.datepicker
                        fieldPlaceholder="@lang('Select End Date')"
                        fieldId="end_date"
                        :fieldLabel="__('End Date')"
                        fieldName="end_date"
                        fieldRequired="true" />
                </div>
                <div class="modal-footer">
                    <x-forms.button-primary id="save-fiscal-year">@lang('app.save')</x-forms.button-primary>
                    <x-forms.button-cancel data-dismiss="modal">@lang('app.cancel')</x-forms.button-cancel>
                </div>
            </x-form>
        </div>
    </div>
</div>

<script>
$('#save-fiscal-year').click(function() {
    const url = "{{ route('accounting.fiscal-years.store') }}";

    $.easyAjax({
        url: url,
        container: '#fiscal-year-form',
        type: "POST",
        data: $('#fiscal-year-form').serialize(),
        success: function(response) {
            if (response.status == 'success') {
                $('#createFiscalYearModal').modal('hide');
                window.location.reload();
            }
        }
    });
});
</script>
@endsection
