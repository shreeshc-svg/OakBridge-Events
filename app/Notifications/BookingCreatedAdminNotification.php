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
        return (new MailMessage)
                    ->subject('New Booking'.' '.$this->data['booking_id'])
                    ->greeting('Hello Admin,')
                    ->line('A new booking has been made with the following details:')
                    ->line('**Booking Details:**')
                    ->line('Booking ID: '. $this->data['booking_id'])
                    ->line('Name: '. $this->data['name'])
                    ->line('Email: '. $this->data['email'])
                    ->line('Phone: '. $this->data['phone'])
                    ->line('Company: '. $this->data['company'] ?? 'NA')
                    ->line('Designation: '. $this->data['designation'] ??  'NA')
                    ->line('Event Date: '. $this->data['date'])
                    ->line('IP: '. $this->data['ip'])
                    ->line('Thank you!');
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