<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name'=>'Community Service','description'=>'Neighborhood & community help'],
            ['name'=>'Environment','description'=>'Cleanups, sustainability, planting'],
            ['name'=>'Education','description'=>'Tutoring & workshops'],
            ['name'=>'Health','description'=>'Awareness & support'],
        ];
        foreach ($items as $i) {
            Category::firstOrCreate(['name'=>$i['name']], $i);
        }
    }
}
