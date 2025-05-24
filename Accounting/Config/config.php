<?php
return [
    'name' => 'Accounting',
    
    // Default account codes for automatic entries
    'default_accounts' => [
        'cash' => '1112', // Cash in Bank
        'accounts_receivable' => '1120',
        'accounts_payable' => '2110',
        'sales_revenue' => '4110',
        'service_revenue' => '4120',
        'retained_earnings' => '3120',
    ],
    
    // Journal entry settings
    'journal_settings' => [
        'auto_post' => false, // Auto-post journal entries
        'require_reference' => false, // Require reference for all entries
        'allow_future_dates' => true, // Allow future dated entries
    ],
    
    // Report settings
    'reports' => [
        'default_date_range' => 'current_month',
        'show_zero_balances' => false,
        'currency_symbol' => '$',
    ],
    
    // Integration settings
    'integrations' => [
        'auto_create_entries' => [
            'invoices' => true,
            'payments' => true,
            'expenses' => true,
            'payroll' => true,
        ],
    ],
];