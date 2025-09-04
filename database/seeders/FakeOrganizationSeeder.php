<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organization;

class FakeOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Create an owner user (business email + password)
            $owner = User::firstOrCreate(
                ['email' => 'support@swaeduae.ae'],
                [
                    'name' => 'SawaedUAE Support',
                    'password' => Hash::make('234026.Hg'),
                    'email_verified_at' => now(), // mark verified for convenience
                ]
            );

            // Make sure the upload path exists in public storage
            $dest = public_path('storage/uploads/org_licenses');
            if (!is_dir($dest)) {
                @mkdir($dest, 0775, true);
            }
            $sample = $dest . '/sample_license.pdf';
            if (!file_exists($sample)) {
                file_put_contents($sample, "%PDF-1.4\n% Fake sample license PDF for seeding\n");
            }

            // Create the organization
            Organization::updateOrCreate(
                ['email' => 'support@swaeduae.ae'], // unique key if you have it
                [
                    'owner_id' => $owner->id,
                    'name' => 'SawaedUAE Support Org',      // REQUIRED in your schema
                    'name_en' => 'SawaedUAE Support Org',
                    'name_ar' => 'جمعية سواعد الإمارات',
                    'emirate' => 'Abu Dhabi',
                    'org_type' => 'Community',
                    'logo_path' => null,

                    // business/contact info
                    'email' => 'support@swaeduae.ae',
                    'public_email' => 'support@swaeduae.ae',
                    'mobile' => '+971501234567',
                    'website' => 'https://swaeduae.ae',
                    'address' => 'Abu Dhabi, UAE',
                    'description' => 'Seeded test organization for admin review and approval flow.',
                    'volunteer_programs' => null,
                    'contact_person_name' => 'Support Team',
                    'contact_person_email' => 'support@swaeduae.ae',
                    'contact_person_phone' => '+971501234567',

                    // license fields — use your actual column names
                    'license_number' => 'LIC-2025-001',
                    'license_file_path' => '/storage/uploads/org_licenses/sample_license.pdf',
                    'wants_license' => true,
                    'license_status' => 'in_review', // requested|in_review|approved|rejected

                    // org login fields (your table has these)
                    'password' => Hash::make('234026.Hg'),

                    // acceptances
                    'tos_accepted_at' => now(),
                    'policy_accepted_at' => now(),
                ]
            );
        });
    }
}
