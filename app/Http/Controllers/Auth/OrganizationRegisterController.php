<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganizationRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class OrganizationRegisterController extends Controller
{
    public function create()
    {
        return view('auth.organization_register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name'      => ['required','string','max:255'],
            'trade_license_number'   => ['required','string','max:255'],
            'email'                  => ['required','email','max:255','unique:users,email'],
            'password'               => ['required','string','min:8'],
            'phone'                  => ['required','string','max:50'],
            'website'                => ['nullable','url','max:255'],
            'emirate'                => ['nullable','string','max:120'],
            'city'                   => ['nullable','string','max:120'],
            'address'                => ['nullable','string','max:255'],
            'contact_person_name'    => ['required','string','max:255'],
            'contact_person_email'   => ['required','email','max:255'],
            'contact_person_phone'   => ['required','string','max:50'],
            'sector'                 => ['nullable','string','max:120'],
            'description'            => ['nullable','string','max:1000'],
            'terms'                  => ['accepted'],
        ]);

        // Create user with 'org' role
        $user = User::create([
            'name'     => $validated['organization_name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('org');
        } elseif (property_exists($user, 'is_org')) {
            $user->is_org = 1; $user->save();
        }

        // Persist full registration details (kept separate to avoid schema issues)
        OrganizationRegistration::create([
            'user_id'               => $user->id,
            'organization_name'     => $validated['organization_name'],
            'trade_license_number'  => $validated['trade_license_number'],
            'phone'                 => $validated['phone'],
            'website'               => $validated['website'] ?? null,
            'emirate'               => $validated['emirate'] ?? null,
            'city'                  => $validated['city'] ?? null,
            'address'               => $validated['address'] ?? null,
            'contact_person_name'   => $validated['contact_person_name'],
            'contact_person_email'  => $validated['contact_person_email'],
            'contact_person_phone'  => $validated['contact_person_phone'],
            'sector'                => $validated['sector'] ?? null,
            'description'           => $validated['description'] ?? null,
            'status'                => 'pending',
        ]);

        // Log the user in after registration
        auth()->login($user);

        return redirect()->intended('/admin')->with('status', __('Organization account created. Pending review by admin.'));
    }
}
