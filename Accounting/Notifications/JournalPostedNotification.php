<?php
// =======================
// FILE: Accounting/Notifications/JournalPostedNotification.php
// =======================

namespace Modules\Accounting\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Accounting\Entities\Journal;

class JournalPostedNotification extends Notification
{
    protected $journal;

    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Journal Entry Posted')
            ->line('A journal entry has been posted to the accounting system.')
            ->line('Journal Number: ' . $this->journal->journal_number)
            ->line('Amount: ' . currency_format($this->journal->total_debit))
            ->line('Description: ' . $this->journal->description)
            ->action('View Journal Entry', route('accounting.journals.show', $this->journal->id));
    }

    public function toArray($notifiable)
    {
        return [
            'journal_id' => $this->journal->id,
            'journal_number' => $this->journal->journal_number,
            'amount' => $this->journal->total_debit,
            'description' => $this->journal->description,
        ];
    }
}
