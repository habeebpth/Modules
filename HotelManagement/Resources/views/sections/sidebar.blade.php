@if (
    !in_array('client', user_roles()) &&
        (in_array('hotelmanagement', user_modules())))
    <x-menu-item icon="hotel" :text="__('app.menu.HotelManagement')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M2 10V2h10v8m-6-6h4m-4 4h4M2 14h10m-4-2v2" stroke="currentColor" stroke-width="2" fill="none"
                stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>


        <div class="accordionItemContent pb-2">
            <x-sub-menu-item :link="route('hm-properties.index')" :text="__('app.properties')" />
            <x-sub-menu-item :link="route('hm-guests.index')" :text="__('app.menu.HMguests')" />
            <x-sub-menu-item :link="route('hm-rooms.index')" :text="__('app.hmRooms')" />
            <x-sub-menu-item :link="route('hm-checkin.index')" :text="__('app.hmcheckin')" />
        </div>
    </x-menu-item>
@endif
