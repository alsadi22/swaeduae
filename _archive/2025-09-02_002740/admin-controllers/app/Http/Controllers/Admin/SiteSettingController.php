<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index() {
        $s = Setting::all()->pluck('value','key')->map(fn($v)=> json_decode($v,true) ?? $v)->toArray();
        return view('admin.settings.index', compact('s'));
    }

    public function update(Request $r) {
        $data = $r->validate([
            'site_name'   => 'nullable|string|max:120',
            'primary_hex' => 'nullable|string|max:7',
            'logo'        => 'nullable|image|max:2048',
            'hero'        => 'nullable|image|max:4096',
            'social_json' => 'nullable|string',
        ]);

        if ($r->hasFile('logo')) {
            $p = $r->file('logo')->store('site','public');
            Setting::set('site.logo_url', Storage::url($p));
        }
        if ($r->hasFile('hero')) {
            $p = $r->file('hero')->store('site','public');
            Setting::set('site.hero_url', Storage::url($p));
        }
        if (isset($data['site_name']))   Setting::set('site.name', $data['site_name']);
        if (isset($data['primary_hex'])) Setting::set('site.primary_hex', $data['primary_hex']);
        if (!empty($data['social_json'])) {
            Setting::set('site.social', json_decode($data['social_json'], true) ?: []);
        }

        return back()->with('ok','Settings saved');
    }
}
