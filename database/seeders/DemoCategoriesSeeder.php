<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $cats = ['Environment','Health','Community','Education','Animals','Sports','Arts & Culture','Disaster Relief','Technology'];
        foreach ($cats as $c) {
            if (DB::table('categories')->where('name',$c)->doesntExist()) {
                DB::table('categories')->insert(['name'=>$c,'created_at'=>now(),'updated_at'=>now()]);
            }
        }
    }
}
