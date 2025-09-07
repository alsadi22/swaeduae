<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     * Make sure our sitemap builder is registered.
     */
    protected $commands = [
        \App\Console\Commands\BuildSitemaps::class,
        \App\Console\Commands\RunFullHealth::class,
        \App\Console\Commands\ScanVolunteerAbsences::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Nightly sitemap rebuild at 03:20
        $schedule->command('swaed:build-sitemaps')->dailyAt('03:20');
        // Nightly full health check at 03:20
        $schedule->command('swaed:full-health')->dailyAt('03:20');

        $schedule->command('scan:volunteer-absences')->everyFiveMinutes();
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
