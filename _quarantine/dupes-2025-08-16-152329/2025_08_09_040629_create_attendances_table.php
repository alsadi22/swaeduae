<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('opportunity_id')->nullable();
                $table->timestamp('check_in_at')->nullable();
                $table->timestamp('check_out_at')->nullable();
                $table->decimal('hours',5,2)->default(0);
                $table->timestamps();
            });
        } else {
            // Ensure expected columns exist (idempotent)
            Schema::table('attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('attendances','check_in_at'))  $table->timestamp('check_in_at')->nullable();
                if (!Schema::hasColumn('attendances','check_out_at')) $table->timestamp('check_out_at')->nullable();
                if (!Schema::hasColumn('attendances','hours'))       $table->decimal('hours',5,2)->default(0);
            });
        }
    }
    public function down(): void {
        // Keep data safe in production
        // Schema::dropIfExists('attendances');
    }
};
