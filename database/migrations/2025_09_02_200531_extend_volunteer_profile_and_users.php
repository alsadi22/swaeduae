<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // volunteer_profiles: demographic & ID fields (if not present)
        if (Schema::hasTable('volunteer_profiles')) {
            Schema::table('volunteer_profiles', function (Blueprint $t) {
                foreach ([
                    ['name_ar','string',160],
                    ['gender','string',20],
                    ['dob','date',null],
                    ['nationality','string',80],
                    ['emirate','string',80],
                    ['city','string',120],
                    ['emirates_id','string',50],
                    ['emirates_id_expiry','date',null],
                    ['avatar_path','string',190],
                    ['skills','json',null],
                    ['interests','json',null],
                    ['availability','json',null],
                ] as $f) {
                    [$col,$type,$len] = $f;
                    if (!Schema::hasColumn('volunteer_profiles',$col)) {
                        match($type) {
                            'string' => $t->string($col, $len)->nullable(),
                            'date'   => $t->date($col)->nullable(),
                            'json'   => $t->json($col)->nullable(),
                            default  => null,
                        };
                    }
                }
            });
        }

        // users: consent/otp helper fields
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                if (!Schema::hasColumn('users','terms_accepted_at')) $t->timestamp('terms_accepted_at')->nullable()->after('remember_token');
                if (!Schema::hasColumn('users','mobile_verified_at')) $t->timestamp('mobile_verified_at')->nullable()->after('terms_accepted_at');
                if (!Schema::hasColumn('users','phone')) $t->string('phone', 60)->nullable()->after('mobile');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('volunteer_profiles')) {
            Schema::table('volunteer_profiles', function (Blueprint $t) {
                foreach (['name_ar','gender','dob','nationality','emirate','city','emirates_id','emirates_id_expiry','avatar_path','skills','interests','availability'] as $c) {
                    if (Schema::hasColumn('volunteer_profiles',$c)) $t->dropColumn($c);
                }
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                foreach (['terms_accepted_at','mobile_verified_at','phone'] as $c) {
                    if (Schema::hasColumn('users',$c)) $t->dropColumn($c);
                }
            });
        }
    }
};
