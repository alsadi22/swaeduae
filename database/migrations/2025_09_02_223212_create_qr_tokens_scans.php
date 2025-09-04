<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  if (!Schema::hasTable('qr_tokens')) Schema::create('qr_tokens', function (Blueprint $t) {
    $t->id(); $t->unsignedBigInteger('event_id')->index(); $t->string('token',64)->unique();
    $t->timestamp('expires_at')->nullable(); $t->unsignedBigInteger('created_by')->nullable(); $t->timestamps();
  });
  if (!Schema::hasTable('qr_scans')) Schema::create('qr_scans', function (Blueprint $t) {
    $t->id(); $t->unsignedBigInteger('token_id')->index(); $t->unsignedBigInteger('user_id')->nullable()->index();
    $t->decimal('lat',10,7)->nullable(); $t->decimal('lng',10,7)->nullable(); $t->timestamp('scanned_at')->nullable();
    $t->json('device')->nullable(); $t->timestamps();
  });
} public function down(): void { Schema::dropIfExists('qr_scans'); Schema::dropIfExists('qr_tokens'); } };
