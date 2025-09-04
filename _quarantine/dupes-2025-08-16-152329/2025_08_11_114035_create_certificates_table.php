<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('opportunity_id')->nullable();
                $table->string('code',16)->unique();
                $table->decimal('hours',5,2)->default(0);
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();
                $table->index(['user_id','opportunity_id']);
            });
        } else {
            Schema::table('certificates', function (Blueprint $table) {
                if (!Schema::hasColumn('certificates','code'))  $table->string('code',16)->unique();
                if (!Schema::hasColumn('certificates','hours')) $table->decimal('hours',5,2)->default(0);
                if (!Schema::hasColumn('certificates','issued_at')) $table->timestamp('issued_at')->nullable();
            });
        }
    }
    public function down(): void {
        // Schema::dropIfExists('certificates');
    }
};
