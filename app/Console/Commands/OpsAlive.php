<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OpsAlive extends Command
{
    protected $signature = 'ops:alive';
    protected $description = 'Send a daily alive notification and log a heartbeat';

    public function handle(): int
    {
        $when = now()->toIso8601String();
        Log::info('ops.alive', ['at' => $when]);

        $to = env('MONITOR_EMAIL');
        if ($to) {
            try {
                Mail::raw("swaeduae.ae alive @ {$when}", function($m) use ($to) {
                    $m->to($to)->subject('swaeduae.ae: daily alive');
                });
                $this->info("Alive email sent to {$to}");
            } catch (\Throwable $e) {
                $this->warn('Mail send failed: '.$e->getMessage());
            }
        } else {
            $this->line('MONITOR_EMAIL not set; logged only.');
        }

        return Command::SUCCESS;
    }
}
