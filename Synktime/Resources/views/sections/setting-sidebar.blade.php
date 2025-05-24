@if (
    !in_array('client', user_roles()) &&
        (in_array('synktime', user_modules())))
<x-setting-menu-item :active="$activeMenu" menu="synktime_settings" :href="route('synktime-settings.index')"
:text="__('synktime::app.menu.SynktimeSettings')"/>
@endif
