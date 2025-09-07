<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('location_pings', function (Blueprint $t) {
            $t->index(['user_id','created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('location_pings', function (Blueprint $t) {
            $t->dropIndex(['user_id','created_at']);
        });
    }
};
