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
        Commands\leadReport::class,
        Commands\whatsappRenewal::class,
        Commands\RenewalReminder::class,
        commands\NewCustomerCount::class,
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('leadreport:daily')->daily();
        $schedule->command('whatsapprenewal:daily')->dailyAt('10.46');
        $schedule->command('renewal:reminder')->dailyAt('10.46');
        $schedule->command('renewal:reminder')->daily('10.46');
        $schedule->command('check:customer')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
