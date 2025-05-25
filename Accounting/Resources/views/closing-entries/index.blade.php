@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-md-12">
            <x-cards.data :title="__('Year-End Closing')">
                <div class="row">
                    <div class="col-md-6">
                        <x-forms.select fieldId="fiscal_year_id" :fieldLabel="__('Select Fiscal Year to Close')"
                            fieldName="fiscal_year_id">
                            <option value="">@lang('Select Fiscal Year')</option>
                            @foreach($fiscalYears as $year)
                                <option value="{{ $year->id }}" {{ $year->is_closed ? 'disabled' : '' }}>
                                    {{ $year->name }} {{ $year->is_closed ? '(Closed)' : '' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <x-forms.button-primary id="close-year" icon="lock">
                            @lang('Close Fiscal Year')
                        </x-forms.button-primary>
                    </div>
                </div>
            </x-cards.data>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <x-cards.data :title="__('Closing Entries History')">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>@lang('Fiscal Year')</th>
                                <th>@lang('Journal #')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($closingEntries as $entry)
                            <tr>
                                <td>{{ $entry->fiscalYear->name }}</td>
                                <td>{{ $entry->journal->journal_number }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($entry->type) {
                                            'revenue' => 'badge-success',
                                            'expense' => 'badge-danger',
                                            'dividend' => 'badge-warning',
                                            'summary' => 'badge-info',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($entry->type) }}</span>
                                </td>
                                <td>{{ $entry->closing_date->format(company()->date_format) }}</td>
                                <td>{{ currency_format($entry->amount) }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>
                                    <a href="{{ route('accounting.journals.show', $entry->journal_id) }}" class="btn btn-sm btn-outline-primary">
                                        @lang('View Journal')
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">@lang('No closing entries found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-cards.data>
        </div>
    </div>
</div>

<script>
$('#close-year').click(function() {
    const fiscalYearId = $('#fiscal_year_id').val();

    if (!fiscalYearId) {
        Swal.fire({
            title: '@lang("Select Fiscal Year")',
            text: '@lang("Please select a fiscal year to close")',
            icon: 'warning'
        });
        return;
    }

    Swal.fire({
        title: '@lang("Close Fiscal Year?")',
        text: '@lang("This action cannot be undone. All revenue and expense accounts will be closed to retained earnings.")',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '@lang("Yes, Close Year")',
        cancelButtonText: '@lang("Cancel")'
    }).then((result) => {
        if (result.isConfirmed) {
            $.easyAjax({
                url: "{{ route('accounting.closing-entries.close') }}",
                type: "POST",
                data: {
                    fiscal_year_id: fiscalYearId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endsection
