<?php

// Add these translations to your language files (e.g., en.json or resources/lang/en/app.php)

return [
    // Menu Items
    'menu.journalEntries' => 'Journal Entries',

    // Actions
    'addJournalEntry' => 'Add Journal Entry',
    'editJournalEntry' => 'Edit Journal Entry',
    'viewJournalEntry' => 'View Journal Entry',
    'post' => 'Post',
    'void' => 'Void',

    // Labels
    'journalEntries' => 'Journal Entries',
    'journalEntry' => 'Journal Entry',
    'journalEntryItems' => 'Journal Entry Items',
    'referenceNumber' => 'Reference Number',
    'debit' => 'Debit',
    'credit' => 'Credit',
    'difference' => 'Difference',
    'balance' => 'Balance',
    'addItem' => 'Add Item',
    'createdBy' => 'Created By',
    'postedBy' => 'Posted By',
    'draft' => 'Draft',
    'posted' => 'Posted',
    'voided' => 'Voided',

    // Messages
    'messages' => [
        'journalEntryAddedSuccessfully' => 'Journal entry added successfully.',
        'journalEntryUpdatedSuccessfully' => 'Journal entry updated successfully.',
        'journalEntryDeletedSuccessfully' => 'Journal entry deleted successfully.',
        'journalEntryPostedSuccessfully' => 'Journal entry posted successfully.',
        'journalEntryVoidedSuccessfully' => 'Journal entry voided successfully.',
        'journalEntryNotBalanced' => 'Journal entry must be balanced (debits must equal credits).',
        'minimumTwoItemsRequired' => 'At least two items are required for a journal entry.',
        'cannotEditPostedJournalEntry' => 'Posted journal entries cannot be edited.',
        'journalEntryCannotBePosted' => 'This journal entry cannot be posted.',
        'journalEntryCannotBeVoided' => 'This journal entry cannot be voided.',
    ],

    // Confirmation Messages
    'confirmation' => [
        'postJournalEntry' => 'Post Journal Entry',
        'postJournalEntryText' => 'Are you sure you want to post this journal entry? Once posted, it cannot be edited.',
        'voidJournalEntry' => 'Void Journal Entry',
        'voidJournalEntryText' => 'Are you sure you want to void this journal entry?',
    ],

    // Placeholders
    'placeholders' => [
        'referenceNumber' => 'Enter reference number',
        'date' => 'Select date',
    ],
];
