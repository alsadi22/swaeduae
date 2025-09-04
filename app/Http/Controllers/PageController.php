<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function partners()
    {
        return view('pages.partners');
    }

    public function contactForm()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:190'],
            'message' => ['required','string','max:5000'],
        ]);

        // Send via configured mailer (defaults to 'log' on your host)
        $to = config('mail.from.address', 'admin@swaeduae.ae');
        try {
            Mail::raw(
                "New contact message from {$data['name']} <{$data['email']}>\n\n{$data['message']}",
                function ($m) use ($to) {
                    $m->to($to)->subject('SawaedUAE: New Contact Message');
                }
            );
            return back()->with('status', __('Thanks! Your message has been sent.'));
        } catch (\Throwable $e) {
            return back()->withErrors(['message' => __('Could not send message. Please try again later.')]);
        }
    }
}
