<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\BookingCreated::class => [
            \App\Listeners\BookingNotifyAdmin::class,
            \App\Listeners\BookingNotifyUser::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Admin > Emails sent: a copy of every email the website sends
        Event::listen(\Illuminate\Mail\Events\MessageSending::class, fn ($e) => \App\Support\MailLogger::sending($e));
        Event::listen(\Illuminate\Mail\Events\MessageSent::class, fn ($e) => \App\Support\MailLogger::sent($e));
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}