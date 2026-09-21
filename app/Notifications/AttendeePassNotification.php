<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * One email per pass holder, once the order is paid.
 * The buyer gets the full receipt separately.
 */
class AttendeePassNotification extends Notification
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
            ->subject('Your pass for ' . ($this->data['event'] ?? 'our event'))
            ->replyTo(Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.attendeePass', ['data' => $this->data]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
