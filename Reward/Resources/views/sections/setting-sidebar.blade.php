@if (
    !in_array('client', user_roles()) &&
        (in_array('hotelmanagement', user_modules())))
<x-setting-menu-item :active="$activeMenu" menu="hotel_managment_settings" :href="route('hotelmanagement-settings.index')"
:text="__('hotelmanagement::app.menu.hotelmanagmentSettings')"/>
@endif
