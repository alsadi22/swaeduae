<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function edit()
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $settings = [];

        if ($orgId) {
            $org = DB::table('organizations')->where('id',$orgId)->first();
            if ($org) {
                foreach (['primary_color','logo_path','name','address','phone','website','twitter','facebook','instagram'] as $k) {
                    if (isset($org->$k)) $settings[$k] = $org->$k;
                }
            }
            // Fallback to settings table
            foreach (['primary_color','logo_path','name','address','phone','website','twitter','facebook','instagram'] as $k) {
                if (empty($settings[$k])) {
                    $settings[$k] = DB::table('settings')->where('key', "org:{$orgId}:{$k}")->value('value');
                }
            }
        }

        return view('org.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'primary_color' => ['nullable','regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'logo'          => ['nullable','image','max:4096'],
            'name'          => ['nullable','string','max:255'],
            'address'       => ['nullable','string','max:1000'],
            'phone'         => ['nullable','string','max:50'],
            'website'       => ['nullable','url','max:255'],
            'twitter'       => ['nullable','string','max:255'],
            'facebook'      => ['nullable','string','max:255'],
            'instagram'     => ['nullable','string','max:255'],
        ]);

        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        if (!$orgId) return back()->withErrors(['org' => 'Organization not found for this account.']);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = "org{$orgId}_" . time() . "." . $file->getClientOriginalExtension();
            $stored = $file->storeAs('uploads/org_branding', $name, 'public');
            $logoPath = "/storage/" . $stored;
        }

        $updates = [];
        foreach (['primary_color','name','address','phone','website','twitter','facebook','instagram'] as $k) {
            if (!empty($data[$k])) $updates[$k] = $data[$k];
        }
        if (!empty($logoPath)) $updates['logo_path'] = $logoPath;

        if (!empty($updates)) {
            if (Schema::hasTable('organizations')) {
                DB::table('organizations')->where('id', $orgId)->update($updates + ['updated_at'=>now()]);
            } else {
                foreach ($updates as $k => $v) {
                    DB::table('settings')->updateOrInsert(
                        ['key' => "org:{$orgId}:{$k}"],
                        ['value' => $v, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }

        return back()->with('status', __('Settings updated'));
    }
}
