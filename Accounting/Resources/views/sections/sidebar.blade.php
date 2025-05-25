@if (!in_array('client', user_roles()) && in_array('accounting', user_modules()))
    <x-menu-item icon="calculator" :text="__('Accounting')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M9 7h6l2 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2h2z" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2" stroke="currentColor"
                  stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
        </x-slot>

        <div class="accordionItemContent pb-2">
            <!-- MAIN DASHBOARD -->
            <x-sub-menu-item :link="route('accounting.index')" :text="__('Dashboard')" />

            <!-- SETUP & CONFIGURATION -->
            <div class="sub-menu-section">
                <div class="sub-menu-section-title">@lang('Setup')</div>
                <x-sub-menu-item :link="route('accounting.chart-of-accounts.index')" :text="__('Chart of Accounts')" />
                <x-sub-menu-item :link="route('accounting.fiscal-years.index')" :text="__('Fiscal Years')" />
                <x-sub-menu-item :link="route('accounting.tax-codes.index')" :text="__('Tax Codes')" />
            </div>

            <!-- TRANSACTIONS -->
            <div class="sub-menu-section">
                <div class="sub-menu-section-title">@lang('Transactions')</div>
                <x-sub-menu-item :link="route('accounting.journals.index')" :text="__('Journal Entries')" />
                <x-sub-menu-item :link="route('accounting.journals.create')" :text="__('New Journal Entry')" class="openRightModal" />
                <x-sub-menu-item :link="route('accounting.reconciliations.index')" :text="__('Bank Reconciliation')" />
            </div>

            <!-- BUDGETING -->
            <div class="sub-menu-section">
                <div class="sub-menu-section-title">@lang('Budgeting')</div>
                <x-sub-menu-item :link="route('accounting.budgets.index')" :text="__('Budgets')" />
                <x-sub-menu-item :link="route('accounting.budgets.create')" :text="__('Create Budget')" class="openRightModal" />
            </div>

            <!-- REPORTS -->
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
                    <div class="dropdown-divider"></div>
                    <x-sub-menu-item :link="route('accounting.reports.budget-variance')" :text="__('Budget vs Actual')" />
                    <x-sub-menu-item :link="route('accounting.reports.cash-flow')" :text="__('Cash Flow')" />
                </div>
            </div>

            <!-- YEAR-END -->
            <div class="sub-menu-section">
                <div class="sub-menu-section-title">@lang('Year-End')</div>
                <x-sub-menu-item :link="route('accounting.closing-entries.index')" :text="__('Closing Entries')" />
            </div>

            <!-- TOOLS -->
            <div class="sub-menu-dropdown">
                <a href="javascript:;" class="sub-menu-link">
                    <i class="fa fa-tools mr-2"></i>@lang('Tools')
                    <i class="fa fa-chevron-right ml-auto"></i>
                </a>
                <div class="sub-menu-dropdown-content">
                    <x-sub-menu-item :link="route('accounting.import-export.index')" :text="__('Import/Export')" />
                    <x-sub-menu-item :link="route('accounting.audit.index')" :text="__('Audit Trail')" />
                    <div class="dropdown-divider"></div>
                    <x-sub-menu-item :link="route('accounting.settings.index')" :text="__('Settings')" />
                </div>
            </div>
        </div>
    </x-menu-item>

    <style>
    /* Clean section styling */
    .sub-menu-section {
        margin-bottom: 12px;
    }

    .sub-menu-section-title {
        padding: 6px 16px 4px 16px;
        font-weight: 600;
        font-size: 11px;
        color: #858796;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 4px;
    }

    /* Dropdown styling */
    .sub-menu-dropdown {
        position: relative;
        margin-bottom: 8px;
    }

    .sub-menu-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: 0;
        background-color: #fff;
        min-width: 220px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        border-radius: 6px;
        border: 1px solid #e3e6f0;
        padding: 8px 0;
        transform: translateX(calc(100% + 10px));
    }

    .sub-menu-dropdown:hover .sub-menu-dropdown-content {
        display: block;
    }

    .sub-menu-link {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: #5a5c69;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
        border-radius: 4px;
        margin: 0 8px;
    }

    .sub-menu-link:hover {
        background-color: #eaecf4;
        color: #3a3b45;
        text-decoration: none;
    }

    .sub-menu-link i.fa-chevron-right {
        font-size: 10px;
        transition: transform 0.2s;
        opacity: 0.6;
    }

    .sub-menu-dropdown:hover .sub-menu-link i.fa-chevron-right {
        transform: rotate(90deg);
    }

    /* Dropdown divider */
    .dropdown-divider {
        height: 1px;
        margin: 8px 0;
        background-color: #e3e6f0;
    }

    /* Sub-menu items within dropdowns */
    .sub-menu-dropdown-content .sub-menu-item {
        margin: 0;
        border-radius: 0;
    }

    .sub-menu-dropdown-content .sub-menu-item:hover {
        background-color: #f8f9fc;
    }

    /* Active states */
    .sub-menu-item.active,
    .sub-menu-link.active {
        background-color: #4e73df !important;
        color: white !important;
    }

    /* Responsive behavior */
    @media (max-width: 768px) {
        .sub-menu-dropdown-content {
            position: static;
            display: block;
            box-shadow: none;
            border: none;
            background-color: #f8f9fc;
            margin: 4px 0;
            padding: 4px;
            transform: none;
        }

        .sub-menu-dropdown .sub-menu-link {
            display: none; /* Hide the dropdown trigger on mobile */
        }

        .sub-menu-section-title {
            font-size: 10px;
            padding: 4px 12px;
        }
    }

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        .sub-menu-dropdown-content {
            background-color: #2d3748;
            border-color: #4a5568;
        }

        .sub-menu-section-title {
            color: #a0aec0;
            border-color: #4a5568;
        }

        .sub-menu-link {
            color: #e2e8f0;
        }

        .sub-menu-link:hover {
            background-color: #4a5568;
            color: #f7fafc;
        }

        .dropdown-divider {
            background-color: #4a5568;
        }
    }

    /* Icon consistency */
    .sub-menu-link i:first-child {
        width: 16px;
        text-align: center;
        margin-right: 8px;
    }

    /* Hover animations */
    .sub-menu-item,
    .sub-menu-link {
        transition: all 0.2s ease;
    }

    /* Badge for notifications (if needed) */
    .sub-menu-badge {
        background-color: #e74a3b;
        color: white;
        border-radius: 8px;
        padding: 1px 5px;
        font-size: 9px;
        font-weight: 600;
        margin-left: auto;
    }
    </style>
@endif
