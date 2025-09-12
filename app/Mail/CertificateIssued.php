<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CertificateIssued extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;

    public function __construct($certificate){ $this->certificate = $certificate; }

    public function build()
    {
        $c = $this->certificate;
        $mail = $this->subject('Your SwaedUAE Certificate ('.($c->code ?? 'CERT').')')
                     ->view('mail.certificate_issued', ['c'=>$c]);

        // Attach only if file exists on the 'public' disk; no extra options
        $path = $c->pdf_path ?? null;              // e.g. "certificates/ABC.pdf" or "storage/certificates/ABC.pdf"
        if ($path) {
            $rel = ltrim(preg_replace('#^storage/#','',$path), '/'); // normalize to "certificates/ABC.pdf"
            if (Storage::disk('public')->exists($rel)) {
                $mail->attachFromStorageDisk('public', $rel);        // let Symfony infer mime/name
            }
        }
        return $mail;
    }
}
