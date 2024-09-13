<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobAlertNotification extends Notification
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
                    ->subject('New Job Application from: '.$this->data['name'])
                    ->line('New Job Application From: ')
                    ->line('Name: '.$this->data['name'])
                    ->line('Email: '.$this->data['email'])
                    ->line('Phone: '.$this->data['phone'])
                    ->line('Message: '.$this->data['message'])
                    ->line('Job: [' . $this->data['url'] . '](' . $this->data['url'] . ')'); // Markdown link

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
