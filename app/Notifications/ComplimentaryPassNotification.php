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

    /**
     * @param array{name: string, event: string, event_date: mixed, venue: ?string,
     *   passes: array<int, array{booking_id: string, holder: string, pass_name: ?string}>, cc: array<int, string>, test?: bool} $data
     */
    public function __construct(public array $data)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $passes = $this->data['passes'];
        $count = count($passes);
        $subject = $count > 1
            ? 'Your ' . $count . ' complimentary passes – ' . $this->data['event']
            : 'Your complimentary pass – ' . $this->data['event'] . ' (' . ($passes[0]['booking_id'] ?? '') . ')';

        $mail = (new MailMessage)
            ->subject((! empty($this->data['test']) ? '[TEST] ' : '') . $subject)
            ->replyTo(Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.complimentaryPass', ['data' => $this->data]);

        if (! empty($this->data['cc'])) {
            $mail->cc($this->data['cc']);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
