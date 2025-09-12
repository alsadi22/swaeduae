<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'name_en')) $table->string('name_en')->nullable()->after('name');
            if (!Schema::hasColumn('organizations', 'name_ar')) $table->string('name_ar')->nullable()->after('name_en');
            if (!Schema::hasColumn('organizations', 'emirate')) $table->string('emirate', 50)->nullable()->after('name_ar');
            if (!Schema::hasColumn('organizations', 'org_type')) $table->string('org_type', 120)->nullable()->after('emirate'); // educational, cultural, charitable, sports...
            if (!Schema::hasColumn('organizations', 'logo_path')) $table->string('logo_path')->nullable()->after('org_type');

            if (!Schema::hasColumn('organizations', 'public_email')) $table->string('public_email')->nullable()->after('logo_path');
            if (!Schema::hasColumn('organizations', 'mobile')) $table->string('mobile', 30)->nullable()->after('public_email');
            if (!Schema::hasColumn('organizations', 'mobile_verified_at')) $table->timestamp('mobile_verified_at')->nullable()->after('mobile');
            if (!Schema::hasColumn('organizations', 'website')) $table->string('website')->nullable()->after('mobile_verified_at');
            if (!Schema::hasColumn('organizations', 'address')) $table->string('address', 255)->nullable()->after('website');

            if (!Schema::hasColumn('organizations', 'description')) $table->text('description')->nullable()->after('address');
            if (!Schema::hasColumn('organizations', 'volunteer_programs')) $table->text('volunteer_programs')->nullable()->after('description');

            if (!Schema::hasColumn('organizations', 'contact_person_name')) $table->string('contact_person_name')->nullable()->after('volunteer_programs');
            if (!Schema::hasColumn('organizations', 'contact_person_email')) $table->string('contact_person_email')->nullable()->after('contact_person_name');
            if (!Schema::hasColumn('organizations', 'contact_person_phone')) $table->string('contact_person_phone', 30)->nullable()->after('contact_person_email');

            if (!Schema::hasColumn('organizations', 'wants_license')) $table->boolean('wants_license')->default(false)->after('contact_person_phone');
            if (!Schema::hasColumn('organizations', 'license_status')) $table->string('license_status', 40)->default('none')->after('wants_license'); // none|requested|in_review|approved|rejected

            if (!Schema::hasColumn('organizations', 'tos_accepted_at')) $table->timestamp('tos_accepted_at')->nullable()->after('license_status');
            if (!Schema::hasColumn('organizations', 'policy_accepted_at')) $table->timestamp('policy_accepted_at')->nullable()->after('tos_accepted_at');
        });
    }

    public function down(): void
    {
        // Safe rollback: drop only if exists
        Schema::table('organizations', function (Blueprint $table) {
            foreach ([
                'name_en','name_ar','emirate','org_type','logo_path','public_email','mobile','mobile_verified_at',
                'website','address','description','volunteer_programs','contact_person_name','contact_person_email',
                'contact_person_phone','wants_license','license_status','tos_accepted_at','policy_accepted_at'
            ] as $col) {
                if (Schema::hasColumn('organizations', $col)) $table->dropColumn($col);
            }
        });
    }
};
