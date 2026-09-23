<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** A nudge to a buyer whose order is still waiting to be paid. */
class PaymentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public array $data)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->data['subject'])
            ->replyTo(Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.paymentReminder', ['data' => $this->data]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
