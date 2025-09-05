<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CertificateEmailController extends Controller
{
    public function send(Certificate $certificate)
    {
        Mail::send('emails.general_notification', [
            'subject' => 'Your Participation Certificate',
            'messageBody' => 'Please find your certificate attached.'
        ], function ($message) use ($certificate) {
            $message->to($certificate->user->email)
                    ->subject('Participation Certificate')
                    ->attach(storage_path('app/public/' . $certificate->certificate_path));
        });

        return redirect()->back()->with('success', 'Certificate emailed successfully.');
    }
}
