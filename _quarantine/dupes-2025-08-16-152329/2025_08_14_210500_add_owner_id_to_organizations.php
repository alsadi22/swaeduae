<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
                // If you want FK and you have users table name 'users':
                // $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'owner_id')) {
                // $table->dropForeign(['owner_id']); // uncomment if you added the FK above
                $table->dropColumn('owner_id');
            }
        });
    }
};
