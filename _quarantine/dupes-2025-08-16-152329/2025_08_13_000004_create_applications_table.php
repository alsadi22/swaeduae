<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('opportunity_id')->index();
                $table->enum('status',['pending','approved','rejected','cancelled'])->default('pending')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['user_id','opportunity_id']);
            });
        }
    }
    public function down(): void { /* keep */ }
};
