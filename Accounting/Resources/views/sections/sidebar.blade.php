@if (!in_array('client', user_roles()) && in_array('accounting', user_modules()))
    <x-menu-item icon="calculator" :text="__('Accounting')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M9 7h6l2 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2h2z" stroke="currentColor" 
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="currentColor" 
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>

        <div class="accordionItemContent pb-2">
            <x-sub-menu-item :link="route('accounting.index')" :text="__('Dashboard')" />
            
            <x-sub-menu-item :link="route('accounting.chart-of-accounts.index')" :text="__('Chart of Accounts')" />
            
            <x-sub-menu-item :link="route('accounting.journals.index')" :text="__('Journal Entries')" />
            
            <!-- Reports Dropdown -->
            <div class="sub-menu-dropdown">
                <a href="javascript:;" class="sub-menu-link">
                    <i class="fa fa-chart-bar mr-2"></i>@lang('Reports')
                    <i class="fa fa-chevron-right ml-auto"></i>
                </a>
                <div class="sub-menu-dropdown-content">
                    <x-sub-menu-item :link="route('accounting.reports.trial-balance')" :text="__('Trial Balance')" />
                    <x-sub-menu-item :link="route('accounting.reports.balance-sheet')" :text="__('Balance Sheet')" />
                    <x-sub-menu-item :link="route('accounting.reports.income-statement')" :text="__('Income Statement')" />
                    <x-sub-menu-item :link="route('accounting.reports.general-ledger')" :text="__('General Ledger')" />
                </div>
            </div>
        </div>
    </x-menu-item>
@endif