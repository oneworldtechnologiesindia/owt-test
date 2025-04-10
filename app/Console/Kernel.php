<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\OfferStatusUpdate::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('offerStatusUpdate:cron')->everyFiveMinutes();
        $schedule->command('AppointmentStatusUpdate:cron')->everyFiveMinutes();
        $schedule->command('ResetDealerStatusLevel:cron')->yearly();
        $schedule->command('DealerContractUpdate:cron')->daily();
        $schedule->command('CheckSubscriptionStatus:cron')->daily();
        $schedule->command('CheckAccountStatus:cron')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
