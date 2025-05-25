@if (!in_array('client', user_roles()) && in_array('accounting', user_modules()))
    <x-menu-item icon="calculator" :text="__('accounting::app.accounting')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M9 7h6l2 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2h2z" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>

        <div class="accordionItemContent pb-2">
            {{-- Core Features --}}
            <x-sub-menu-item :link="route('accounting.index')" :text="__('accounting::app.dashboard')" />
            <x-sub-menu-item :link="route('accounting.chart-of-accounts.index')" :text="__('accounting::app.chartOfAccounts')" />
            <x-sub-menu-item :link="route('accounting.journals.index')" :text="__('accounting::app.journalEntries')" />

            {{-- Budget & Planning --}}
            <x-sub-menu-item :link="route('accounting.budgets.index')" :text="__('accounting::app.budgets')" />
            <x-sub-menu-item :link="route('accounting.fiscal-years.index')" :text="__('accounting::app.fiscalYears')" />

            {{-- Banking & Reconciliation --}}
            <x-sub-menu-item :link="route('accounting.reconciliations.index')" :text="__('accounting::app.bankReconciliation')" />

            {{-- Tax Management --}}
            <x-sub-menu-item :link="route('accounting.tax-codes.index')" :text="__('accounting::app.taxCodes')" />

            {{-- Reports --}}
            <x-sub-menu-item :link="route('accounting.reports.trial-balance')" :text="__('accounting::app.trialBalance')" />
            <x-sub-menu-item :link="route('accounting.reports.balance-sheet')" :text="__('accounting::app.balanceSheet')" />
            <x-sub-menu-item :link="route('accounting.reports.income-statement')" :text="__('accounting::app.incomeStatement')" />
            <x-sub-menu-item :link="route('accounting.reports.general-ledger')" :text="__('accounting::app.generalLedger')" />

            {{-- Year End & Settings --}}
            <x-sub-menu-item :link="route('accounting.closing-entries.index')" :text="__('accounting::app.yearEndClosing')" />
            <x-sub-menu-item :link="route('accounting.settings.index')" :text="__('accounting::app.accountingSettings')" />
        </div>
    </x-menu-item>
@endif
