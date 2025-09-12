<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrganizationRegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'org_name'            => ['required','string','max:255'],
            'email'               => ['required','email','max:255'],
            'password'            => ['nullable','string','min:8'],
            'phone'               => ['nullable','string','max:50'],
            'website'             => ['nullable','string','max:255'],
            'city'                => ['nullable','string','max:100'],
            'emirate'             => ['nullable','string','max:100'],
            'address'             => ['nullable','string','max:255'],
            'contact_person_name' => ['nullable','string','max:255'],
            'contact_person_email'=> ['nullable','email','max:255'],
            'contact_person_phone'=> ['nullable','string','max:50'],
            'sector'              => ['nullable','string','max:100'],
            'about'               => ['nullable','string','max:2000'],
            'logo'                => ['nullable','image','max:2048'],
            'license'             => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:4096'],
        ]);

        $paths = [];
        if ($request->hasFile('logo')) {
            $paths['logo_path'] = $request->file('logo')->store('org-uploads/logos', 'public');
        }
        if ($request->hasFile('license')) {
            $paths['license_path'] = $request->file('license')->store('org-uploads/licenses', 'public');
        }

        // TODO: replace with your real Organization model/save logic
        DB::table('organization_registrations')->insert([
            'org_name'   => $data['org_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'website'    => $data['website'] ?? null,
            'city'       => $data['city'] ?? null,
            'emirate'    => $data['emirate'] ?? null,
            'address'    => $data['address'] ?? null,
            'contact_person_name'  => $data['contact_person_name'] ?? null,
            'contact_person_email' => $data['contact_person_email'] ?? null,
            'contact_person_phone' => $data['contact_person_phone'] ?? null,
            'sector'     => $data['sector'] ?? null,
            'about'      => $data['about'] ?? null,
            'logo_path'  => $paths['logo_path'] ?? null,
            'license_path'=> $paths['license_path'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('org.login')->with('status', 'Organization request submitted. We will review and enable your account.');
    }
}
