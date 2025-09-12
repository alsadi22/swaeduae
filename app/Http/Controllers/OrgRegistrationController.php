<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Storage;

class OrgRegistrationController extends Controller
{
    public function create()
    {
        return view('organizations.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => [
                'required','email','max:255',
                function($attr,$value,$fail){
                    $domain = strtolower(substr(strrchr($value,'@')?:'',1));
                    if ($domain === '') return;
                    $free = [
                        'gmail.com','yahoo.com','outlook.com','hotmail.com','aol.com',
                        'icloud.com','proton.me','protonmail.com','live.com','msn.com',
                        'yandex.com','mail.ru'
                    ];
                    if (in_array($domain,$free,true)) {
                        $fail(__('Please use a business email (free email domains are not allowed).'));
                    }
                }
            ],
            'phone'       => 'nullable|string|max:255',
            'website'     => 'nullable|url|max:255',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:255',
            'emirate'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
        ]);

        $data['approved'] = false;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/logos');
            $data['logo_path'] = Storage::url($path);
        }

        Organization::create($data);

        return redirect()->route('organizations.register')->with('status', 'Thank you! Your organization application was submitted and awaits admin approval.');
    }

    public function pending()
    {
        $pending = Organization::where('approved', false)->latest()->paginate(20);
        return view('admin.organizations.pending', compact('pending'));
    }

    public function approve(Organization $organization)
    {
        $organization->update(['approved'=>true]);
        return back()->with('status', 'Organization approved.');
    }

    public function reject(Organization $organization)
    {
        // You can also soft-delete or capture reason here
        $organization->delete();
        return back()->with('status', 'Organization rejected and removed.');
    }
}
