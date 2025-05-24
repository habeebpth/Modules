@if (!in_array('client', user_roles()) && in_array('travels', user_modules()))
    <x-setting-menu-item :active="$activeMenu" menu="travel_settings" :href="route('travel-settings.index')" :text="__('travels::app.menu.TravelSettings')" />
@endif
