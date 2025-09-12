<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DebugLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $label = 'QTEST') {}

    public function handle(): void
    {
        \Log::info($this->label.': job START '.now());
        usleep(300000);
        \Log::info($this->label.': job END   '.now());
    }
}
