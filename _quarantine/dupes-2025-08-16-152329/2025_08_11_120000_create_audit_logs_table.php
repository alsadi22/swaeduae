<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action')->index();
            $t->string('method', 10)->nullable();
            $t->string('route_name')->nullable()->index();
            $t->string('path')->nullable();
            $t->string('model_type')->nullable();
            $t->string('model_id')->nullable();
            $t->json('payload')->nullable();      // sanitized request input
            $t->json('meta')->nullable();         // ip, ua, status, etc.
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('audit_logs');
    }
};
