<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoCertificateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('certificates')->insert([
            'user_id'          => 1,
            'event_id'         => 1,
            'certificate_number' => 'CERT-001',
            'verification_code'  => 'VERIFY-123',
            'title'            => 'Volunteer Achievement',
            'description'      => 'Awarded for outstanding volunteer service.',
            'issued_date'      => Carbon::now()->toDateString(),
            'file_path'        => null,
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now(),
        ]);
    }
}
