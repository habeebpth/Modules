
{{-- Use this version LATER when you add more accounting features --}}

@if (
    !in_array('client', user_roles()) &&
        (in_array('accounting', user_modules())))
    <x-menu-item icon="calculator" :text="__('app.menu.accounting')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M9 7h6l2 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2h2z" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>

        <div class="accordionItemContent pb-2">
            {{-- Core Features --}}
            <x-sub-menu-item :link="route('accounting.index')" :text="__('app.dashboard')" />
            <x-sub-menu-item :link="route('accounting.chart-of-accounts.index')" :text="__('app.chartOfAccounts')" />
            <x-sub-menu-item :link="route('accounting.journals.index')" :text="__('app.journalEntries')" />

            {{-- Budget Management (when implemented) --}}
            <x-sub-menu-item :link="route('accounting.budgets.index')" :text="__('app.budgets')" />
            <x-sub-menu-item :link="route('accounting.fiscal-years.index')" :text="__('app.fiscalYears')" />

            {{-- Banking --}}
            <x-sub-menu-item :link="route('accounting.reconciliations.index')" :text="__('app.bankReconciliation')" />

            {{-- Tax Management --}}
            <x-sub-menu-item :link="route('accounting.tax-codes.index')" :text="__('app.taxCodes')" />

            {{-- Reports --}}
            <x-sub-menu-item :link="route('accounting.reports.trial-balance')" :text="__('app.trialBalance')" />
            <x-sub-menu-item :link="route('accounting.reports.balance-sheet')" :text="__('app.balanceSheet')" />
            <x-sub-menu-item :link="route('accounting.reports.income-statement')" :text="__('app.incomeStatement')" />
            <x-sub-menu-item :link="route('accounting.reports.general-ledger')" :text="__('app.generalLedger')" />

            {{-- Year End --}}
            <x-sub-menu-item :link="route('accounting.closing-entries.index')" :text="__('app.yearEndClosing')" />

            {{-- Settings --}}
            <x-sub-menu-item :link="route('accounting.settings.index')" :text="__('app.accountingSettings')" />
        </div>
    </x-menu-item>
@endif
