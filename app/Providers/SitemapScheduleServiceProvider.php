<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class SitemapScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register tasks after the Schedule is resolved
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $log = storage_path('logs/sitemap.cron.log');

            $schedule->command('sitemap:generate')
                ->dailyAt('02:30')
                ->onOneServer()
                ->appendOutputTo($log);

            $schedule->command('swaed:build-sitemaps')
                ->dailyAt('02:35')
                ->onOneServer()
                ->appendOutputTo($log);
        });
    }
}
