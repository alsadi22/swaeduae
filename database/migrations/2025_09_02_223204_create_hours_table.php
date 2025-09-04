<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  if (!Schema::hasTable('hours')) Schema::create('hours', function (Blueprint $t) {
    $t->id(); $t->unsignedBigInteger('user_id')->index(); $t->unsignedBigInteger('opportunity_id')->nullable()->index();
    $t->integer('minutes')->default(0); $t->timestamp('awarded_at')->nullable();
    $t->unsignedBigInteger('attendance_id')->nullable()->index(); $t->json('meta')->nullable(); $t->timestamps();
  });
} public function down(): void { Schema::dropIfExists('hours'); } };
