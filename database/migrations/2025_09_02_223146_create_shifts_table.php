<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  if (!Schema::hasTable('shifts')) Schema::create('shifts', function (Blueprint $t) {
    $t->id(); $t->unsignedBigInteger('event_id')->index();
    $t->string('title',120)->nullable(); $t->dateTime('starts_at'); $t->dateTime('ends_at'); $t->timestamps();
  });
} public function down(): void { Schema::dropIfExists('shifts'); } };
