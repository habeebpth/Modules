@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <x-cards.data :title="__('Journal Entry Details')" padding="false">
                <!-- Journal Header -->
                <div class="p-4 border-bottom">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Journal Number')</label>
                                <p class="f-15 text-dark f-w-500">{{ $journal->journal_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Date')</label>
                                <p class="f-15 text-dark">{{ $journal->date->format(company()->date_format) }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Description')</label>
                                <p class="f-15 text-dark">{{ $journal->description }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Status')</label>
                                <p class="f-15">
                                    @if($journal->status === 'draft')
                                        <span class="badge badge-warning">@lang('Draft')</span>
                                    @elseif($journal->status === 'posted')
                                        <span class="badge badge-success">@lang('Posted')</span>
                                    @elseif($journal->status === 'reversed')
                                        <span class="badge badge-danger">@lang('Reversed')</span>
                                    @endif
                                </p>
                            </div>
                            @if($journal->reference_type)
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Reference Type')</label>
                                <p class="f-15 text-dark">{{ ucfirst(str_replace('_', ' ', $journal->reference_type)) }}</p>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="f-14 text-dark-grey mb-1">@lang('Created At')</label>
                                <p class="f-15 text-dark">{{ $journal->created_at->format(company()->date_format . ' h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            @if($journal->status === 'draft')
                                <a href="{{ route('accounting.journals.edit', $journal->id) }}" class="btn btn-primary openRightModal mr-2">
                                    <i class="fa fa-edit mr-1"></i>@lang('Edit')
                                </a>
                                <button type="button" class="btn btn-success post-journal mr-2" data-id="{{ $journal->id }}">
                                    <i class="fa fa-check mr-1"></i>@lang('Post Entry')
                                </button>
                            @elseif($journal->status === 'posted')
                                <button type="button" class="btn btn-warning reverse-journal mr-2" data-id="{{ $journal->id }}">
                                    <i class="fa fa-undo mr-1"></i>@lang('Reverse Entry')
                                </button>
                            @endif
                            <a href="{{ route('accounting.journals.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left mr-1"></i>@lang('Back to List')
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Journal Entries -->
                <div class="p-4">
                    <h5 class="mb-3">@lang('Journal Entries')</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="15%">@lang('Account Code')</th>
                                    <th width="35%">@lang('Account Name')</th>
                                    <th width="25%">@lang('Description')</th>
                                    <th width="12.5%" class="text-right">@lang('Debit')</th>
                                    <th width="12.5%" class="text-right">@lang('Credit')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journal->entries as $entry)
                                <tr>
                                    <td>{{ $entry->account->account_code }}</td>
                                    <td>{{ $entry->account->account_name }}</td>
                                    <td>{{ $entry->description ?: $journal->description }}</td>
                                    <td class="text-right">
                                        {{ $entry->debit > 0 ? currency_format($entry->debit) : '-' }}
                                    </td>
                                    <td class="text-right">
                                        {{ $entry->credit > 0 ? currency_format($entry->credit) : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="text-right">@lang('Total')</th>
                                    <th class="text-right">{{ currency_format($journal->total_debit) }}</th>
                                    <th class="text-right">{{ currency_format($journal->total_credit) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">@lang('Balance Check')</th>
                                    <th colspan="2" class="text-center">
                                        @if($journal->isBalanced())
                                            <span class="badge badge-success">@lang('Balanced')</span>
                                        @else
                                            <span class="badge badge-danger">@lang('Not Balanced')</span>
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($journal->status === 'reversed')
                <!-- Reversal Information -->
                <div class="p-4 border-top bg-light">
                    <h6 class="text-danger mb-2">@lang('Reversal Information')</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">@lang('Reversed At'): {{ $journal->reversed_at ? $journal->reversed_at->format(company()->date_format . ' h:i A') : 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">@lang('This entry has been reversed and is no longer active')</small>
                        </div>
                    </div>
                </div>
                @endif
            </x-cards.data>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Post journal entry
$('.post-journal').click(function() {
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
                        window.location.reload();
                    }
                }
            });
        }
    });
});

// Reverse journal entry
$('.reverse-journal').click(function() {
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
                        window.location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endpush
