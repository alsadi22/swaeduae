<?php
namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\OrgProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // GET /org/register
    public function create()
    {
        return view('org.auth.register');
    }

    // POST /org/register  (route name: org.register.submit)
    public function submit(Request $r)
    {
        $r->validate([
            'org_name' => ['required','string','max:160'],
            'name'     => ['required','string','max:120'],
            'email'    => ['required','email','max:190', Rule::unique('users','email')],
            'password' => ['required','confirmed','min:8'],
            'emirate'  => ['nullable','string','max:80'],
            'phone'    => ['nullable','string','max:60'],
            'website'  => ['nullable','string','max:190'],
            'about'    => ['nullable','string','max:3000'],
        ]);

        // Create a user account (no org role yet)
        $user = User::create([
            'name'     => $r->name,
            'email'    => $r->email,
            'password' => Hash::make($r->password),
        ]);

        // Optional: give volunteer by default; org role only after approval
        if (method_exists($user,'assignRole')) {
            try { $user->assignRole('volunteer'); } catch (\Throwable $e) {}
        }

        // Store org profile as pending
        OrgProfile::create([
            'user_id' => $user->id,
            'org_name'=> $r->org_name,
            'emirate' => $r->emirate,
            'phone'   => $r->phone,
            'website' => $r->website,
            'about'   => $r->about,
            'status'  => 'pending',
        ]);

        return view('org.register_thanks', ['email'=>$user->email, 'org'=>$r->org_name]);
    }
}
