<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GrievenceNotification extends Notification
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
            ->subject('Grievence Form')
            ->line('New Grievence Form Received')
            ->line('Name: ' . $this->data['name'])
            ->line('Email: ' . $this->data['email'])
            ->line('Phone: ' . $this->data['phone'])
            ->line('Recruited City: ' . $this->data['city'])
            ->line('Recruited Country: ' . $this->data['country'])
            ->line('Employer: ' . $this->data['employer'])
            ->line('Recruited Date: ' . $this->data['date'])
            ->line('Subject: ' . $this->data['subject'])
            ->line('Message: ' . $this->data['message'])
            ->line('Thank you for using our application!');
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
