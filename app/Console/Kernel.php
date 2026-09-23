<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** Commands registered one by one (the Commands folder is not auto-loaded). */
    protected $commands = [
        \App\Console\Commands\ClearRegistrations::class,
        \App\Console\Commands\SendPaymentReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * Only runs if cron calls `schedule:run`. The reminder command is also safe
     * to put straight into cron on its own (`php artisan orders:remind`), which
     * is the simpler setup on shared hosting.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('orders:remind')->hourly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // $this->load(__DIR__.'/Commands');

        // require base_path('routes/console.php');
    }
}
