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
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Nightly sitemap rebuild at 03:20
        $schedule->command('swaed:build-sitemaps')->dailyAt('03:20');
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
