<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('event_volunteer')) {
            Schema::create('event_volunteer', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->string('status')->default('pending');
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->unique(['event_id','user_id']);
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('event_volunteer');
    }
};
