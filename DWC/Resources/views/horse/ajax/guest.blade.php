<div class="d-lg-flex">
    <div class="w-100 py-0 py-lg-3 py-md-0 ">

        <!-- HOTEL RESERVATION DETAILS START -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <x-cards.data :title="__('app.Horse') . ' ' . __('app.menu.guests')"
                    otherClasses="d-flex justify-content-between align-items-center p-4">

                    <div class="table-responsive p-20">
                        <x-table class="table-bordered">
                            <x-slot name="thead">
                                <th>#</th>
                                <th>@lang('app.name')</th>
                                <th>@lang('modules.guests.guest_type')</th>
                                <th>@lang('app.company')</th>
                                <th>@lang('app.email')</th>
                                <th>@lang('app.phone')</th>
                                <th>@lang('app.country')</th>
                                <th>@lang('app.passportNumber')</th>
                                <th>@lang('modules.guests.amendment_date')</th>
                            </x-slot>

                            @forelse($guests as $key => $guest)
                                <tr class="row{{ $guest->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ ucfirst($guest->salutation ?? '') . ' ' . ($guest->first_name ?? '-') . ' ' . ($guest->last_name ?? '-') }}</td>
                                    <td>{{ optional($guest->guesttype)->name ?? '-' }}</td>
                                    <td>{{ $guest->company ?? '-' }}</td>
                                    <td>{{ $guest->email ?? '-' }}</td>
                                    <td>{{ optional($guest->guestcountrycode)->phonecode ? '+' . optional($guest->guestcountrycode)->phonecode . ' ' . $guest->mobile : '-' }}</td>
                                    <td>{{ optional($guest->guestcountry)->name ?? '-' }}</td>
                                    <td>{{ $guest->passport_number ?? '-' }}</td>
                                    <td>{{ $guest->amendment_date ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <x-cards.no-record icon="list" :message="__('messages.ThisHorsenoGuests')" />
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
