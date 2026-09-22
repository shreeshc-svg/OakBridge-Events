<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedAdminNotification extends Notification 
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
        $quote = $data['quote'] ?? null;

        $mail = (new MailMessage)
            ->subject(match (true) {
                $order && $order->isPaid() => 'PAID: order ' . $order->order_no . ' – ' . ($data['event'] ?? ''),
                ! empty($data['payment_failed']) => 'Payment failed: order ' . $order->order_no . ' – ' . ($data['event'] ?? ''),
                (bool) $order => 'New order (unpaid) ' . $order->order_no . ' – ' . ($data['event'] ?? ''),
                default => 'New registration ' . ($data['booking_id'] ?? '') . ' – ' . ($data['event'] ?? ''),
            })
            ->greeting('Hello Admin,')
            ->line(match (true) {
                $order && $order->isPaid() => 'Payment received – the passes have been issued and everyone emailed:',
                ! empty($data['payment_failed']) => 'A payment attempt failed. The passes are still held:',
                (bool) $order => 'A new order has been placed (payment still to be confirmed):',
                default => 'A new registration has come in:',
            })
            ->line('Event: ' . ($data['event'] ?? 'NA'))
            ->line('Name: ' . $data['name'])
            ->line('Email: ' . $data['email'])
            ->line('Phone: ' . $data['phone'])
            ->line('Company: ' . (($data['company'] ?? null) ?: 'NA'))
            ->line('Designation: ' . (($data['designation'] ?? null) ?: 'NA'));

        if ($order && $quote) {
            $mail->line('Pass: ' . $order->pass_name . ' × ' . $order->quantity)
                ->line('Amount: ' . \App\Support\Pricing::money((float) $order->total)
                    . ($quote['discount'] > 0 ? ' (after ' . $quote['discount_label'] . ')' : ''))
                ->line('Status: ' . $order->statusLabel())
                ->action('Open the order', url('/admin/orders/' . $order->id));
        }

        foreach ($data['attendees'] ?? [] as $index => $attendee) {
            if ($index > 0) {
                $mail->line('Attendee ' . ($index + 1) . ': ' . $attendee['name'] . ' (' . $attendee['email'] . ')');
            }
        }

        return $mail->line('IP: ' . ($data['ip'] ?? 'NA'));
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