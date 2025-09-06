<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class RunFullHealth extends Command
{
    protected $signature = 'swaed:full-health';

    protected $description = 'Run tools/full_health.sh and store report under public/health';

    public function handle(): int
    {
        $script = base_path('tools/full_health.sh');
        if (! is_file($script)) {
            $this->error('tools/full_health.sh not found');

            return 1;
        }

        $outputDir = public_path('health');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $ts = now()->format('Y-m-d_His');
        $log = $outputDir.'/full_health_'.$ts.'.log';
        $process = Process::path(base_path())->run("bash $script > $log 2>&1");

        if ($process->successful()) {
            $this->info('Health check written to '.$log);

            return 0;
        }

        $this->error('Health check failed');

        return 1;
    }
}
