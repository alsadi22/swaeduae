<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('opportunities')) {
            Schema::table('opportunities', function (Blueprint $table) {
                if (!Schema::hasColumn('opportunities','organizer_id'))  $table->unsignedBigInteger('organizer_id')->nullable()->after('id');
                if (!Schema::hasColumn('opportunities','location'))      $table->string('location')->nullable()->after('city');
                if (!Schema::hasColumn('opportunities','checkin_token')) $table->string('checkin_token',64)->nullable()->after('ends_at');
                if (!Schema::hasColumn('opportunities','checkout_token'))$table->string('checkout_token',64)->nullable()->after('checkin_token');
                if (!Schema::hasColumn('opportunities','is_completed'))  $table->boolean('is_completed')->default(false)->after('checkout_token');
            });
        }
    }
    public function down(): void {
        // Do not drop in production
        // Schema::table('opportunities', function (Blueprint $table) {
        //     foreach (['organizer_id','location','checkin_token','checkout_token','is_completed'] as $col) {
        //         if (Schema::hasColumn('opportunities',$col)) $table->dropColumn($col);
        //     }
        // });
    }
};
