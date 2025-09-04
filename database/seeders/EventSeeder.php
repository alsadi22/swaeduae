<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'title' => 'حملة تنظيف الشاطئ',
            'description' => 'انضم إلينا لتنظيف شاطئ جميرا والحفاظ على البيئة.',
            'date' => Carbon::now()->addDays(7),
            'location' => 'شاطئ جميرا، دبي',
        ]);

        Event::create([
            'title' => 'زيارة دار المسنين',
            'description' => 'فرصة لزيارة دار المسنين ورسم البسمة على وجوههم.',
            'date' => Carbon::now()->addDays(14),
            'location' => 'دار المسنين، الشارقة',
        ]);
    }
}
