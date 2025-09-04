<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueueHealth extends Command
{
    protected $signature = 'app:queue-health {--failed-threshold=0} {--pending-threshold=50}';
    protected $description = 'Check queue health and raise alerts when thresholds are exceeded';

    public function handle(): int
    {
        $pending = (int) DB::table('jobs')->count();
        $failed  = (int) DB::table('failed_jobs')->count();
        $ft = (int) $this->option('failed-threshold');
        $pt = (int) $this->option('pending-threshold');

        $msg = "queue-health pending=$pending failed=$failed thresholds[pending>$pt, failed>$ft]";
        $this->info($msg);

        if ($failed > $ft || $pending > $pt) {
            Log::error("ALERT $msg");
            $this->error("ALERT $msg");
            return self::FAILURE;
        }

        Log::info("OK $msg");
        return self::SUCCESS;
    }
}
