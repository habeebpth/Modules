@if (!in_array('client', user_roles()) && in_array('accounting', user_modules()))
    <x-menu-item icon="calculator" :text="__('accounting::app.accounting')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M9 7h6l2 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2h2z" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12 12h.01M12 16h.01" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>

        <div class="accordionItemContent pb-2">
            {{-- Dashboard --}}
            <x-sub-menu-item :link="route('accounting.index')" :text="__('accounting::app.dashboard')" />

            {{-- Setup Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.setup')
            </div>
            <x-sub-menu-item :link="route('accounting.chart-of-accounts.index')" :text="__('accounting::app.chartOfAccounts')" />
            <x-sub-menu-item :link="route('accounting.fiscal-years.index')" :text="__('accounting::app.fiscalYears')" />
            <x-sub-menu-item :link="route('accounting.tax-codes.index')" :text="__('accounting::app.taxCodes')" />

            {{-- Transactions Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.transactions')
            </div>
            <x-sub-menu-item :link="route('accounting.journals.index')" :text="__('accounting::app.journalEntries')" />
            <x-sub-menu-item :link="route('accounting.journals.create')" :text="__('accounting::app.newJournalEntry')" />

            {{-- Banking Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('Banking')
            </div>
            <x-sub-menu-item :link="route('accounting.reconciliations.index')" :text="__('accounting::app.bankReconciliation')" />

            {{-- Budgeting Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.budgeting')
            </div>
            <x-sub-menu-item :link="route('accounting.budgets.index')" :text="__('accounting::app.budgets')" />
            <x-sub-menu-item :link="route('accounting.budgets.create')" :text="__('accounting::app.createBudget')" />

            {{-- Reports Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.reports')
            </div>
            <x-sub-menu-item :link="route('accounting.reports.trial-balance')" :text="__('accounting::app.trialBalance')" />
            <x-sub-menu-item :link="route('accounting.reports.balance-sheet')" :text="__('accounting::app.balanceSheet')" />
            <x-sub-menu-item :link="route('accounting.reports.income-statement')" :text="__('accounting::app.incomeStatement')" />
            <x-sub-menu-item :link="route('accounting.reports.general-ledger')" :text="__('accounting::app.generalLedger')" />

            {{-- Year-End Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.yearEnd')
            </div>
            <x-sub-menu-item :link="route('accounting.closing-entries.index')" :text="__('accounting::app.yearEndClosing')" />

            {{-- Tools Section --}}
            <div class="sidebar-submenu-header text-uppercase text-muted f-11 px-3 py-2 mt-2">
                @lang('accounting::app.tools')
            </div>
            <x-sub-menu-item :link="route('accounting.import-export.index')" :text="__('accounting::app.importExport')" />
            <x-sub-menu-item :link="route('accounting.settings.index')" :text="__('accounting::app.accountingSettings')" />
        </div>
    </x-menu-item>
@endif
