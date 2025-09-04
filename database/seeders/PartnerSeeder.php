<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['name'=>'Sawaed Foundation','website'=>'https://example.org'],
            ['name'=>'UAE Green Initiative','website'=>'https://example.com'],
        ];
        foreach ($partners as $p) {
            Partner::firstOrCreate(['name'=>$p['name']], $p);
        }
    }
}
