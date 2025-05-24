@if (
    !in_array('client', user_roles()) &&
        (in_array('dwc', user_modules())))
<x-menu-item icon="hotel" :text="__('app.menu.DWC')" :addon="App::environment('demo')">
    <x-slot name="iconPath">
        <path d="M2 10V2h10v8m-6-6h4m-4 4h4M2 14h10m-4-2v2" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    </x-slot>


    <div class="accordionItemContent pb-2">
        <x-sub-menu-item :link="route('horses.index')" :text="__('app.Horses')" />
        <x-sub-menu-item :link="route('guests.index')" :text="__('app.menu.guests')" />
        <x-sub-menu-item :link="route('dwc.arrivals.index')" :text="__('app.Arrivals')" />
        <x-sub-menu-item :link="route('dwc.departures.index')" :text="__('app.Departures')" />
        <x-sub-menu-item :link="route('hotel-reservations.index')" :text="__('app.HotelReservations')" />
        <x-sub-menu-item :link="route('hotels.index')" :text="__('app.Hotels')" />
        <x-sub-menu-item :link="route('guest-type.index')" :text="__('app.Guesttypes')" />
        <x-sub-menu-item :link="route('billing-code.index')" :text="__('app.BillingCodes')" />
    </div>
</x-menu-item>
@endif
