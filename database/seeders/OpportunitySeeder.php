<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opportunity;
use App\Models\Category;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::first();
        Opportunity::firstOrCreate(
            ['title'=>'Weekend Beach Cleanup'],
            [
                'description'=>'Help keep our beaches clean. Gloves and bags provided.',
                'start_date'=>now()->addDays(7),
                'end_date'=>now()->addDays(7)->addHours(3),
                'location'=>'Dubai',
                'category_id'=>$cat?->id,
                'organization_id'=>null,
                'is_virtual'=>false,
                'volunteers_required'=>50,
            ]
        );
    }
}
