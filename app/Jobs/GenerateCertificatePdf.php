<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $certificateId) {}

    public function handle(): void
    {
        // TODO: Implement using your existing certificate generator logic:
        // 1) Load certificate data by $this->certificateId
        // 2) Render HTML -> PDF (snappy/dompdf/etc.)
        // 3) Save file to storage/public/certificates and update DB path
    }
}
