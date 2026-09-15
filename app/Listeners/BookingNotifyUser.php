<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\BookingCreatedUserNotificationUpdated;

class BookingNotifyUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        $email = $event->data['email'];
        \Notification::route('mail',$email)->notify(new BookingCreatedUserNotificationUpdated($event->data));
    }
}
