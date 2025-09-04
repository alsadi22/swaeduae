<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('users') && !Schema::hasColumn('users','sv_code')) {
      Schema::table('users', function (Blueprint $t) { $t->string('sv_code', 16)->nullable()->unique()->after('id'); });
    }
    if (Schema::hasTable('org_profiles') && !Schema::hasColumn('org_profiles','org_code')) {
      Schema::table('org_profiles', function (Blueprint $t) { $t->string('org_code', 16)->nullable()->unique()->after('user_id'); });
    }
  }
  public function down(): void {
    if (Schema::hasTable('users') && Schema::hasColumn('users','sv_code')) {
      Schema::table('users', fn(Blueprint $t)=>$t->dropUnique(['sv_code']));
      Schema::table('users', fn(Blueprint $t)=>$t->dropColumn('sv_code'));
    }
    if (Schema::hasTable('org_profiles') && Schema::hasColumn('org_profiles','org_code')) {
      Schema::table('org_profiles', fn(Blueprint $t)=>$t->dropUnique(['org_code']));
      Schema::table('org_profiles', fn(Blueprint $t)=>$t->dropColumn('org_code'));
    }
  }
};
