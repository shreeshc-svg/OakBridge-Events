<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Admin > Ticketing > Complimentary passes: the guest's pass, by email. */
class ComplimentaryPassNotification extends Notification
{
    use Queueable;

    /** @param array{name: string, event: string, event_date: mixed, venue: ?string, booking_id: string, pass_name: ?string, test?: bool} $data */
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
            ->subject((! empty($this->data['test']) ? '[TEST] ' : '') . 'Your complimentary pass – ' . $this->data['event'] . ' (' . $this->data['booking_id'] . ')')
            ->replyTo(Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.complimentaryPass', ['data' => $this->data]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
