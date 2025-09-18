<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        // Honeypot: hidden field _hp must be empty
        if ($request->filled('_hp')) {
            return back()->withErrors(['message' => 'Spam detected.']);
        }

        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email:rfc,dns',
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:5000',
        ]);

        Mail::to(env('CONTACT_TO', 'admin@swaeduae.ae'))->send(new ContactMessage($data));
        return back()->with('status', __('Thanks! Your message was sent.'));
    }
}
