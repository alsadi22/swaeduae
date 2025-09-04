<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use Illuminate\Support\Str;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        $stories = [
            ['title'=>'Beach Cleanup Impact','body'=>'200+kg trash removed; great teamwork!'],
            ['title'=>'Tutoring Program Success','body'=>'45 students improved their grades.'],
        ];
        foreach ($stories as $s) {
            Story::firstOrCreate(
                ['slug'=>Str::slug($s['title'])],
                ['title'=>$s['title'],'body'=>$s['body'],'published_at'=>now()]
            );
        }
    }
}
