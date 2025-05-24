@extends('layouts.app')

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <div class="d-flex flex-column w-tables rounded bg-white">
            <div class="d-flex">
                <div class="px-4 py-3 border-right">
                    <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.status')</span><br>
                    @php
                        $statusClass = [
                            'draft' => 'warning',
                            'posted' => 'success',
                            'voided' => 'danger',
                        ];
                    @endphp
                    <span class="badge badge-{{ $statusClass[$journalEntry->status] }}">{{ ucfirst($journalEntry->status) }}</span>
                </div>
                <div class="px-4 py-3 w-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.referenceNumber')</span><br>
                            <p class="mb-0 f-14 text-dark">{{ $journalEntry->reference_number }}</p>
                        </div>
                        <div>
                            <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.date')</span><br>
                            <p class="mb-0 f-14 text-dark">{{ $journalEntry->entry_date->format(company()->date_format) }}</p>
                        </div>
                        <div>
                            <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.amount')</span><br>
                            <p class="mb-0 f-14 text-dark">{{ currency_format($journalEntry->total_debit, company()->currency) }}</p>
                        </div>
                    </div>

                    @if ($journalEntry->description)
                        <div class="mt-3">
                            <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.description')</span><br>
                            <p class="mb-0 f-14 text-dark">{{ $journalEntry->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="d-flex px-4 py-3 border-top">
                <div class="col-md-4 px-0">
                    <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.createdBy')</span><br>
                    <p class="mb-0 f-14 text-dark">{{ $journalEntry->creator->name }}</p>
                </div>
                <div class="col-md-4">
                    <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.createdAt')</span><br>
                    <p class="mb-0 f-14 text-dark">{{ $journalEntry->created_at->format(company()->date_format . ' ' . company()->time_format) }}</p>
                </div>
                @if ($journalEntry->posted_by)
                    <div class="col-md-4">
                        <span class="f-12 text-dark-grey mb-12 text-capitalize">@lang('app.postedBy')</span><br>
                        <p class="mb-0 f-14 text-dark">{{ $journalEntry->poster->name }} ({{ $journalEntry->posted_at->format(company()->date_format . ' ' . company()->time_format) }})</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="d-flex px-4 py-3 border-top">
                <div class="d-flex">
                    @if ($journalEntry->status == 'draft')
                        <a href="{{ route('journal-entries.edit', $journalEntry->id) }}" class="mr-3 btn btn-primary rounded f-14 p-2 openRightModal">
                            <i class="fa fa-edit mr-1"></i> @lang('app.edit')
                        </a>
                        <a href="javascript:;" data-journal-entry-id="{{ $journalEntry->id }}" class="mr-3 btn btn-success rounded f-14 p-2 post-journal-entry">
                            <i class="fa fa-check mr-1"></i> @lang('app.post')
                        </a>
                    @endif
                    
                    @if ($journalEntry->status == 'posted')
                        <a href="javascript:;" data-journal-entry-id="{{ $journalEntry->id }}" class="mr-3 btn btn-warning rounded f-14 p-2 void-journal-entry">
                            <i class="fa fa-ban mr-1"></i> @lang('app.void')
                        </a>
                    @endif
                    
                    <a href="javascript:;" data-journal-entry-id="{{ $journalEntry->id }}" class="btn btn-danger rounded f-14 p-2 delete-journal-entry">
                        <i class="fa fa-trash mr-1"></i> @lang('app.delete')
                    </a>
                </div>
            </div>

            <!-- Journal Entry Items -->
            <div class="d-flex flex-column w-100 p-4 border-top">
                <h5 class="mb-4">@lang('app.journalEntryItems')</h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('app.account')</th>
                                <th>@lang('app.description')</th>
                                <th class="text-right">@lang('app.debit')</th>
                                <th class="text-right">@lang('app.credit')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journalEntry->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->account->name }} ({{ $item->account->code }})
                                    </td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td class="text-right">
                                        @if ($item->debit > 0)
                                            {{ currency_format($item->debit, company()->currency) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($item->credit > 0)
                                            {{ currency_format($item->credit, company()->currency) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">@lang('app.total')</th>
                                <th class="text-right">{{ currency_format($journalEntry->total_debit, company()->currency) }}</th>
                                <th class="text-right">{{ currency_format($journalEntry->total_credit, company()->currency) }}</th>
                            </tr>
                        </tfoot>
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
            // Post Journal Entry
            $('.post-journal-entry').click(function() {
                var id = $(this).data('journal-entry-id');
                
                Swal.fire({
                    title: "@lang('messages.confirmation.postJournalEntry')",
                    text: "@lang('messages.confirmation.postJournalEntryText')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('app.yes')",
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
                        var url = "{{ route('journal-entries.post') }}";
                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'id': id
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            });
            
            // Void Journal Entry
            $('.void-journal-entry').click(function() {
                var id = $(this).data('journal-entry-id');
                
                Swal.fire({
                    title: "@lang('messages.confirmation.voidJournalEntry')",
                    text: "@lang('messages.confirmation.voidJournalEntryText')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('app.yes')",
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
                        var url = "{{ route('journal-entries.void') }}";
                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'id': id
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            });
            
            // Delete Journal Entry
            $('.delete-journal-entry').click(function() {
                var id = $(this).data('journal-entry-id');
                
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
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
                        var url = "{{ route('journal-entries.destroy', $journalEntry->id) }}";
                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'DELETE'
                            },
                            success: function(response) {
                                if (response.status == "success") {
                                    window.location.href = response.redirectUrl;
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush