<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationProfileController extends Controller
{
    public function show(Request $request)
    {
        $org = Organization::where('owner_id', $request->user()->id)->first();
        return view('org.profile', ['org' => $org]);
    }

    public function update(Request $request)
    {
        $org = Organization::where('owner_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'name_en' => ['required','string','max:190'],
            'name_ar' => ['nullable','string','max:190'],
            'emirate' => ['required','string','max:50'],
            'org_type' => ['required','string','max:120'],
            'logo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'public_email' => ['nullable','email','max:190'],
            'mobile' => ['required','string','max:30'],
            'website' => ['nullable','url','max:190'],
            'address' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'volunteer_programs' => ['nullable','string'],
            'contact_person_name' => ['required','string','max:190'],
            'contact_person_email' => ['required','email','max:190'],
            'contact_person_phone' => ['required','string','max:30'],
            'wants_license' => ['nullable','boolean'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/org_logos', 'public');
            $org->logo_path = '/storage/' . $path;
        }

        $org->fill([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'emirate' => $validated['emirate'],
            'org_type' => $validated['org_type'],
            'public_email' => $validated['public_email'] ?? $org->public_email,
            'mobile' => $validated['mobile'],
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'volunteer_programs' => $validated['volunteer_programs'] ?? null,
            'contact_person_name' => $validated['contact_person_name'],
            'contact_person_email' => $validated['contact_person_email'],
            'contact_person_phone' => $validated['contact_person_phone'],
            'wants_license' => (bool)($validated['wants_license'] ?? false),
            'license_status' => (isset($validated['wants_license']) && $validated['wants_license'])
                ? ($org->license_status === 'none' ? 'requested' : $org->license_status)
                : $org->license_status,
        ]);

        $org->save();

        return back()->with('status', 'Organization profile updated.');
    }

    public function requestLicense(Request $request)
    {
        $org = Organization::where('owner_id', $request->user()->id)->firstOrFail();
        $org->wants_license = true;
        if ($org->license_status === 'none') {
            $org->license_status = 'requested';
        }
        $org->save();

        return back()->with('status', 'Volunteer Licensing Application requested.');
    }
}
