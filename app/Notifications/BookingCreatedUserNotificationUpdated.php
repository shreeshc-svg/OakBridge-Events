<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedUserNotificationUpdated extends Notification
{
    use Queueable;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->data;
        $order = $data['order'] ?? null;
        $event = $data['event'] ?? 'our event';
        $passes = $order && $order->quantity > 1 ? 'passes' : 'pass';

        $subject = match (true) {
            $order && $order->isPaid() => 'Your ' . $passes . ' for ' . $event . ' – payment received',
            ! empty($data['payment_failed']) => 'Payment could not be completed – ' . $event,
            (bool) $order => 'Complete your payment – ' . $event,
            default => 'Registration received – ' . $event,
        };

        return (new MailMessage)
            ->subject($subject)
            ->replyTo(\App\Models\Setting::find(1)?->email ?: 'info@oakbridge.in')
            ->view('email.userBooking', ['data' => $data]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
