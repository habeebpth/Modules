@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="d-grid d-lg-flex d-md-flex action-bar">
        <div class="flex-grow-1 align-items-center">
            <x-forms.link-primary :link="route('accounting.reconciliations.create')"
                class="mr-3 float-left" icon="plus">
                @lang('New Reconciliation')
            </x-forms.link-primary>
        </div>
    </div>

    <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover border-0 w-100">
                <thead>
                    <tr>
                        <th>@lang('Account')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Statement Balance')</th>
                        <th>@lang('Book Balance')</th>
                        <th>@lang('Difference')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reconciliations as $reconciliation)
                    <tr>
                        <td>{{ $reconciliation->account->account_name }}</td>
                        <td>{{ $reconciliation->reconciliation_date->format(company()->date_format) }}</td>
                        <td>{{ currency_format($reconciliation->statement_balance) }}</td>
                        <td>{{ currency_format($reconciliation->book_balance) }}</td>
                        <td class="{{ $reconciliation->difference == 0 ? 'text-success' : 'text-danger' }}">
                            {{ currency_format($reconciliation->difference) }}
                        </td>
                        <td>
                            @php
                                $badgeClass = match($reconciliation->status) {
                                    'draft' => 'badge-warning',
                                    'completed' => 'badge-success',
                                    'reviewed' => 'badge-primary',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($reconciliation->status) }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    @lang('Actions')
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('accounting.reconciliations.show', $reconciliation->id) }}">@lang('View')</a>
                                    @if($reconciliation->status === 'draft')
                                        <a class="dropdown-item" href="{{ route('accounting.reconciliations.edit', $reconciliation->id) }}">@lang('Edit')</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">@lang('No reconciliations found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
