<?php

namespace App\Listeners;

use App\Events\VolunteerCheckedOut;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class GenerateCertificate
{
    public function handle(VolunteerCheckedOut $event)
    {
        $attendance = $event->attendance;
        $user = $attendance->user;
        $eventData = $attendance->event;

        // Check if certificate already exists
        if (Certificate::where('user_id', $user->id)->where('event_id', $eventData->id)->exists()) {
            return;
        }

        // Generate unique verification code
        $code = strtoupper(Str::random(10));

        // Create certificate DB record
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'event_id' => $eventData->id,
            'name' => $user->name,
            'issued_at' => now(),
            'verification_code' => $code,
            'file_path' => null
        ]);

        // Generate PDF
        $verifyUrl = route('certificates.verify', $code);
        $pdf = Pdf::loadView('certificates.pdf', [
            'user' => $user,
            'event' => $eventData,
            'code' => $code,
            'verifyUrl' => $verifyUrl
        ]);

        $fileName = 'certificates/' . $code . '.pdf';
        $pdf->save(public_path($fileName));

        // Update DB path
        $certificate->update(['file_path' => $fileName]);

        // Send email with PDF
        Mail::send('emails.certificate', [
            'user' => $user,
            'event' => $eventData,
            'verifyUrl' => $verifyUrl
        ], function ($message) use ($user, $fileName) {
            $message->to($user->email)
                ->subject(__('Your Volunteer Certificate'))
                ->attach(public_path($fileName));
        });
    }
}
