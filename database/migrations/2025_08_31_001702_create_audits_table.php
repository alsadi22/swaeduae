<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasTable('audits')) {
      Schema::create('audits', function (Blueprint $t) {
        $t->bigIncrements('id');
        $t->unsignedBigInteger('user_id')->nullable()->index();
        $t->string('action', 64)->index();    // e.g., 'qr.scan','cert.issue','login'
        $t->string('entity', 64)->nullable(); // e.g., 'certificate','event'
        $t->string('entity_id', 64)->nullable();
        $t->json('meta')->nullable();
        $t->timestamp('created_at')->useCurrent();
      });
    }
  }
  public function down(): void { Schema::dropIfExists('audits'); }
};
