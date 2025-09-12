<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasTable('events')) {
      Schema::create('events', function (Blueprint $t) {
        $t->bigIncrements('id');
        $t->string('title', 200);
        $t->text('description')->nullable();
        $t->string('location', 200)->nullable();
        $t->timestamp('starts_at')->nullable()->index();
        $t->timestamp('ends_at')->nullable()->index();
        $t->string('status', 32)->default('draft')->index(); // draft|published|archived
        $t->timestamps();
      });
    }
  }
  public function down(): void { Schema::dropIfExists('events'); }
};
