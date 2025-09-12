<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  if (!Schema::hasTable('attendances')) Schema::create('attendances', function (Blueprint $t) {
    $t->id(); $t->unsignedBigInteger('user_id')->index();
    $t->unsignedBigInteger('opportunity_id')->nullable()->index();
    $t->unsignedBigInteger('event_id')->nullable()->index();
    $t->timestamp('check_in_at')->nullable(); $t->timestamp('check_out_at')->nullable();
    $t->string('source',20)->default('qr'); $t->decimal('lat',10,7)->nullable(); $t->decimal('lng',10,7)->nullable();
    $t->integer('distance_m')->nullable(); $t->integer('minutes_raw')->nullable(); $t->integer('minutes_awarded')->nullable();
    $t->string('status',16)->default('ok'); $t->json('meta')->nullable(); $t->timestamps();
  });
} public function down(): void { Schema::dropIfExists('attendances'); } };
