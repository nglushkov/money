<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('rates:get-usd-ars')->weekdays()->between('12:00', '18:00')->hourly();
        $schedule->command('rates:get')->weekdays()->between('06:00', '12:00')->hourly();
        $schedule->command('backup:run')->twiceDaily(3, 15);
        $schedule->command('app:get-crypto-rates')->hourly();
        $schedule->command('app:notify-planned-expense')->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
