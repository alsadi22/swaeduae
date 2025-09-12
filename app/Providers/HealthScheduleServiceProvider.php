<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class HealthScheduleServiceProvider extends ServiceProvider
{
    public function boot(Schedule $schedule): void
    {
        // Daily alive (03:00 server time)
        $schedule->command('ops:alive')->dailyAt('03:00');

        // Heartbeat every 15 minutes (writes to laravel log)
        $schedule->call(function () {
            Log::info('scheduler.heartbeat', ['at' => now()->toIso8601String()]);
        })->everyFifteenMinutes()->name('scheduler.heartbeat');

        // Queue tick (shared hosting friendly)
        $schedule->command("queue:work --stop-when-empty --queue=default --tries=3 --backoff=10 --timeout=120 --sleep=3")
            ->everyMinute()->name('queue.tick');

        // Restart workers daily (no-op for ticks, safe anyway)
        $schedule->command('queue:restart')->dailyAt('02:05');

        // Daily DB backup 03:30
        $schedule->exec(base_path('tools/db_backup.sh'))->dailyAt('03:30');
    }
}
