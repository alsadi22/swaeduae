<?php

namespace App\Jobs;

use App\Mail\ApplicantDecisionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendApplicantDecisionMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $to = $this->payload['to'] ?? null;
        $args = $this->payload['args'] ?? [];
        if (!$to || empty($args)) return;

        // $args order: decision, opportunityTitle, orgName, note, brandColor, logo
        Mail::to($to)->send(new ApplicantDecisionMail(...$args));
    }
}
