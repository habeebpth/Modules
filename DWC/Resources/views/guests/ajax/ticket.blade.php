<div class="d-lg-flex">
    <div class="w-100 py-0 py-lg-3 py-md-0 ">

        <!-- PROJECT DETAILS START -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <x-cards.data :title="__('app.menu.guests') . ' ' . __('app.tickets')" otherClasses="d-flex justify-content-between align-items-center p-4">
                    <div class="table-responsive p-20">
                        <x-table class="table-bordered">
                            <x-slot name="thead">
                                <th>#</th>
                                <th>@lang('app.flightNumber')</th>
                                <th>@lang('app.departureDate')</th>
                                <th>@lang('app.departureTime')</th>
                                <th>@lang('app.arrivalDate')</th>
                                <th>@lang('app.arrivalTime')</th>
                                <th>@lang('app.flightFrom')</th>
                                <th>@lang('app.flightTo')</th>
                                <th>@lang('app.flightClass')</th>
                                <th>@lang('app.locator')</th>
                                <th>@lang('app.ticketNumber')</th>
                                {{-- <th class="text-right">@lang('app.action')</th> --}}
                            </x-slot>

                            @forelse($guests->flightTickets as $key => $ticket)
                                <tr class="row{{ $ticket->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $ticket->flight_no }}</td>
                                    <td>{{ $ticket->departure_date }}</td>
                                    <td>{{ $ticket->departure_time }}</td>
                                    <td>{{ $ticket->arrival_date }}</td>
                                    <td>{{ $ticket->arrival_time }}</td>
                                    <td>{{ $ticket->departure->name ?? '-' }}</td>
                                    <td>{{ $ticket->arrival->name ?? '-' }}</td>
                                    <td>{{ $ticket->flight_class }}</td>
                                    <td>{{ $ticket->locator }}</td>
                                    <td>{{ $ticket->ticket_number }}</td>
                                    {{-- <td class="text-right">
                                        <div class="task_view">
                                            <a href="javascript:;" data-ticket-id="{{ $ticket->id }}"
                                                class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-ticket">
                                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                            </a>
                                        </div>
                                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                                            <a href="javascript:;" data-ticket-id="{{ $ticket->id }}"
                                                class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-ticket">
                                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                                            </a>
                                        </div>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">
                                        <x-cards.no-record icon="list" :message="__('messages.noTicketsAdded')" />
                                    </td>
                                </tr>
                            @endforelse
                        </x-table>
                    </div>
                </x-cards.data>
            </div>
        </div>

        <!-- PROJECT DETAILS END -->

    </div>
</div>
