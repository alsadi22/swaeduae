<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PartnerIntakeController extends Controller
{
    public function form()
    {
        return view('partners.apply');
    }

    public function submit(Request $request)
    {
        $trap = trim((string)$request->input('company_site','')); // honeypot field (should stay empty)

        $data = $request->validate([
            'org_name'    => 'required|string|max:190',
            'contact_name'=> 'nullable|string|max:190',
            'email'       => 'required|email|max:190',
            'phone'       => 'nullable|string|max:64',
            'website'     => 'nullable|string|max:190',
            'emirate'     => 'nullable|string|max:64',
            'message'     => 'nullable|string|max:5000',
        ]);

        $isBot = $trap !== '';

        if (Schema::hasTable('partner_intake_submissions')) {
            DB::table('partner_intake_submissions')->insert([
                'org_name'    => $data['org_name'],
                'contact_name'=> $data['contact_name'] ?? null,
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'website'     => $data['website'] ?? null,
                'emirate'     => $data['emirate'] ?? null,
                'message'     => $data['message'] ?? null,
                'ip'          => $request->ip(),
                'user_agent'  => substr((string)$request->header('User-Agent'),0,255),
                'is_bot'      => $isBot,
                'status'      => 'new',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return redirect()->back()->with('success', __('Thanks! We will contact you shortly.'));
    }
}
