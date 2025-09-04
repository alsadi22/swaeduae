<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show() { return view('public.contact'); }

    public function send(Request $r)
    {
        $data = $r->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email','max:190'],
            'message' => ['required','string','max:5000'],
            'website' => ['nullable','max:0'], // honeypot
        ]);
        if ($r->filled('website')) { return back()->with('status','Thanks!'); }

        $to = config('mail.from.address', 'admin@swaeduae.ae');
        Mail::send('mail.contact', ['data'=>$data], function($m) use ($to, $data) {
            $m->to($to)->subject('Website contact: '.$data['name']);
            $m->replyTo($data['email'], $data['name']);
        });

        return back()->with('status','Message sent. Thank you!');
    }
}
