<div class="d-lg-flex">
    <div class="w-100 py-0 py-lg-3 py-md-0 ">

        <!-- HOTEL RESERVATION DETAILS START -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <x-cards.data :title="__('app.menu.guests') . ' ' . __('app.hotelreservetion')"
                    otherClasses="d-flex justify-content-between align-items-center p-4">

                    <div class="table-responsive p-20">
                        <x-table class="table-bordered">
                            <x-slot name="thead">
                                <th>#</th>
                                <th>@lang('app.hotelName')</th>
                                <th>@lang('app.checkin_date')</th>
                                <th>@lang('app.checkout_date')</th>
                                <th>@lang('app.roomType')</th>
                                <th>@lang('app.sharingWith')</th>
                                <th>@lang('app.billingCode')</th>
                                <th>@lang('app.noOfNights')</th>
                                <th>@lang('app.ConfirmationNo')</th>
                                <th>Notes</th>
                                {{-- <th class="text-right">@lang('app.action')</th> --}}
                            </x-slot>

                            @forelse($guests->hotelReservations as $key => $reservation)
                                <tr class="row{{ $reservation->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $reservation->hotel->name ?? $reservation->others }}</td>
                                    <td>{{ $reservation->checkin_date ?? '-' }}</td>
                                    <td>{{ $reservation->checkout_date ?? '-' }}</td>
                                    <td>{{ $reservation->room_type ?? '-' }}</td>
                                    <td>{{ $reservation->sharing_with ?? '-' }}</td>
                                    <td>{{ $reservation->billingcode->name ?? '-' }}</td>
                                    <td>{{ $reservation->no_of_nights ?? '-' }}</td>
                                    <td>{{ $reservation->confirmation_no ?? '-' }}</td>
                                    <td>{{ $reservation->note_2 ?? '-' }}</td>
                                    {{-- <td class="text-right">
                                        <div class="task_view">
                                            <a href="javascript:;" data-reservation-id="{{ $reservation->id }}"
                                                class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle edit-reservation">
                                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                            </a>
                                        </div>
                                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                                            <a href="javascript:;" data-reservation-id="{{ $reservation->id }}"
                                                class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle delete-reservation">
                                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                                            </a>
                                        </div>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">
                                        <x-cards.no-record icon="list" :message="__('messages.noReservationsAdded')" />
                                    </td>
                                </tr>
                            @endforelse
                        </x-table>
                    </div>

                </x-cards.data>
            </div>
        </div>
        <!-- HOTEL RESERVATION DETAILS END -->

    </div>
</div>
