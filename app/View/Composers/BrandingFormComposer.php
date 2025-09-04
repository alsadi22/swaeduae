<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandingFormComposer
{
    public function compose(View $view)
    {
        $orgId = auth()->user()->organization_id ?? null;
        $settings = [];

        if ($orgId) {
            if (Schema::hasTable('organizations')) {
                if (Schema::hasColumn('organizations','primary_color')) {
                    $settings['primary_color'] = DB::table('organizations')->where('id',$orgId)->value('primary_color');
                }
                if (Schema::hasColumn('organizations','logo_path')) {
                    $settings['logo_path'] = DB::table('organizations')->where('id',$orgId)->value('logo_path');
                }
            }
            if (Schema::hasTable('settings')) {
                $settings['primary_color'] = $settings['primary_color'] ?? DB::table('settings')->where('key',"org:{$orgId}:primary_color")->value('value');
                $settings['logo_path']     = $settings['logo_path']     ?? DB::table('settings')->where('key',"org:{$orgId}:logo_path")->value('value');
            }
        }

        $view->with('settings', $settings);
    }
}
