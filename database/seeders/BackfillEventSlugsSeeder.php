<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Illuminate\Support\Str;

class BackfillEventSlugsSeeder extends Seeder
{
    public function run(): void
    {
        Event::whereNull('slug')->orWhere('slug', '')->orderBy('id')->chunkById(100, function ($events) {
            foreach ($events as $e) {
                $base = Str::slug($e->title ?: ($e->name ?: ('event-'.$e->id)));
                $slug = $base;
                $i = 1;
                while (Event::where('slug', $slug)->where('id', '!=', $e->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $e->slug = $slug;
                $e->save();
            }
        });
    }
}
