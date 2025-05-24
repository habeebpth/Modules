@if (
    !in_array('client', user_roles()) &&
        (in_array('accounting', user_modules())))
<x-setting-menu-item :active="$activeMenu" menu="accounting_settings" :href="route('accounting-settings.index')"
:text="__('accounting::app.menu.AccountingSettings')"/>
@endif
