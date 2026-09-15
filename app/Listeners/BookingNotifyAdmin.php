<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\BookingCreatedAdminNotification;

class BookingNotifyAdmin
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
        $adminEmail = Setting::first();
        \Notification::route('mail',$adminEmail['email'])->notify(new BookingCreatedAdminNotification($event->data));
    }
}