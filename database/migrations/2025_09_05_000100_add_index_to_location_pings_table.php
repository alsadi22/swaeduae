<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('location_pings')) {
            return;
        }
        // Create the index in a driver-agnostic, idempotent way.
        // If it already exists, ignore the error.
        try {
            Schema::table('location_pings', function (Blueprint $table) {
                // Adjust columns if you used a different pair previously.
                $table->index(['user_id','created_at'], 'lp_user_created_idx');
            });
        } catch (\Throwable $e) {
            // ignore duplicate/exists errors across drivers
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('location_pings')) {
            return;
        }
        try {
            Schema::table('location_pings', function (Blueprint $table) {
                $table->dropIndex('lp_user_created_idx');
            });
        } catch (\Throwable $e) {
            // ignore if missing on a given driver
        }
    }
};
