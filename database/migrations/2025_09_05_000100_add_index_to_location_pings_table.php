<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('location_pings')) return;

        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'location_pings')
            ->where('INDEX_NAME', 'location_pings_user_id_created_at_index')
            ->exists();

        if (!$exists) {
            DB::statement('ALTER TABLE `location_pings` ADD INDEX `location_pings_user_id_created_at_index`(`user_id`, `created_at`)');
        }
    }

    public function down(): void
    {
        try { DB::statement('ALTER TABLE `location_pings` DROP INDEX `location_pings_user_id_created_at_index`'); }
        catch (\Throwable $e) { /* ignore */ }
    }
};
