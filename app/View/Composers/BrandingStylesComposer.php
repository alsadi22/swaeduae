<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandingStylesComposer
{
    public function compose(View $view)
    {
        $orgId = auth()->user()->organization_id ?? null;
        $color = null;

        if ($orgId && Schema::hasTable('organizations') && Schema::hasColumn('organizations','primary_color')) {
            $color = DB::table('organizations')->where('id',$orgId)->value('primary_color');
        } elseif (Schema::hasTable('settings')) {
            $color = DB::table('settings')->where('key',"org:{$orgId}:primary_color")->value('value');
        }

        $view->with('color', $color);
    }
}
