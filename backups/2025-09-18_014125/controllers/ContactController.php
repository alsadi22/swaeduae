<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // GET /contact (optional view)
    public function show()
    {
        $view = view()->exists('public.contact') ? 'public.contact' : 'public.home';
        return view($view);
    }

    // POST /contact
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','string','email','max:255'],
            'message' => ['required','string','max:5000'],
        ]);

        // log the submission (mailer can be wired later)
        Log::info('contact.submit', [
            'name' => $data['name'],
            'email' => $data['email'],
            'len' => strlen($data['message']),
            'ip' => $request->ip(),
            'ua' => substr((string)$request->userAgent(), 0, 200),
        ]);

        return redirect('/contact')->with('status', 'sent');
    }
}
