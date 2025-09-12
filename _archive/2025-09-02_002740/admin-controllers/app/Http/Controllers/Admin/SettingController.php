<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $exists = Schema::hasTable('settings');
        $rows = null;

        if ($exists) {
            $rows = DB::table('settings')
                ->when($q !== '', fn($qry)=>$qry->where(fn($w)=>$w
                    ->where('key','like',"%{$q}%")
                    ->orWhere('name','like',"%{$q}%")
                    ->orWhere('value','like',"%{$q}%")))
                ->orderBy('key')
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.settings.index', compact('rows','exists','q'));
    }

    public function edit()
    {
        // Simple edit screen for site logo & homepage hero
        $logo = DB::table('settings')->where('key','site.logo')->value('value');
        $hero = DB::table('settings')->where('key','home.hero')->value('value');
        return view('admin.settings.edit', compact('logo','hero'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo'=>'nullable|image|max:4096',
            'hero'=>'nullable|image|max:8192',
        ]);

        @mkdir(public_path('uploads'), 0775, true);

        if ($request->hasFile('logo')) {
            $path = 'uploads/'.Str::random(8).'_logo_'.time().'.'.$request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('uploads'), basename($path));
            $this->set('site.logo', $path);
        }

        if ($request->hasFile('hero')) {
            $path = 'uploads/'.Str::random(8).'_hero_'.time().'.'.$request->file('hero')->getClientOriginalExtension();
            $request->file('hero')->move(public_path('uploads'), basename($path));
            $this->set('home.hero', $path);
        }

        return redirect()->route('admin.settings.edit')->with('status', __('Settings updated'));
    }

    protected function set(string $key, $value): void
    {
        $exists = DB::table('settings')->where('key',$key)->exists();
        if ($exists) {
            DB::table('settings')->where('key',$key)->update(['value'=>$value,'updated_at'=>now()]);
        } else {
            DB::table('settings')->insert(['key'=>$key,'value'=>$value,'created_at'=>now(),'updated_at'=>now()]);
        }
    }
}
