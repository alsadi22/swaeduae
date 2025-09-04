<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssuedMail;
use Illuminate\Support\Carbon;

class CertificateService
{
    public function issueFor(User $user, Opportunity $opportunity, float $hours): Certificate
    {
        $now = Carbon::now();
        $issuedDate = $now->toDateString();

        // Create or update certificate record
        $cert = Certificate::firstOrNew([
            'user_id' => $user->id,
            'opportunity_id' => $opportunity->id,
        ]);

        if (!$cert->exists) {
            $cert->code = $cert->code ?? $this->generateHumanCode($issuedDate);
        }

        $cert->verification_code = $cert->verification_code ?: $this->generateHumanCode($issuedDate);
        $cert->certificate_number = $cert->certificate_number ?: $this->generateNumber($issuedDate);
        $cert->issued_date = $issuedDate;
        $cert->issued_at = $now;
        $cert->hours = $hours ?: 0.00;

        // Render PDF
        $pdf = Pdf::loadView('certificates.pdf', [
            'user' => $user,
            'opportunity' => $opportunity,
            'cert' => $cert,
        ])->setPaper('a4', 'landscape');

        $pdfBytes = $pdf->output();

        // Save PDF under /public/certificates/
        $fileName = ($cert->code ?? $cert->verification_code) . '.pdf';
        $publicPath = public_path('certificates');
        if (!is_dir($publicPath)) { @mkdir($publicPath, 0755, true); }
        $fullPath = $publicPath . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($fullPath, $pdfBytes);

        // Normalize file_path (public URL)
        $cert->file_path = '/certificates/' . $fileName;

        // Compute SHA-256 checksum and store
        $cert->checksum = hash('sha256', $pdfBytes);

        $cert->save();

        // Email (HTML)
        try {
            Mail::to($user->email)->send(new CertificateIssuedMail($cert));
        } catch (\Throwable $e) {
            // swallow mail errors on shared hosting
        }

        return $cert;
    }

    public function generateHumanCode(string $date): string
    {
        // e.g., SUyymmdd-XXXXXX
        return 'SU' . Carbon::parse($date)->format('ymd') . '-' . Str::upper(Str::random(6));
    }

    public function generateNumber(string $date): string
    {
        return 'SU-' . Carbon::parse($date)->format('Ymd') . '-' . random_int(100000, 999999);
    }
}
