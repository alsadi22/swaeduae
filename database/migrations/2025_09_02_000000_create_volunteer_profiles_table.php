<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (!Schema::hasTable('volunteer_profiles')) {
      Schema::create('volunteer_profiles', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
        $t->string('phone')->nullable();
        $t->string('emirate')->nullable();     // Abu Dhabi, Dubai, etc.
        $t->string('gender')->nullable();
        $t->date('dob')->nullable();
        $t->json('languages')->nullable();     // ["ar","en"]
        $t->json('skills')->nullable();        // ["first_aid","it_support"]
        $t->text('bio')->nullable();
        $t->string('emergency_name')->nullable();
        $t->string('emergency_phone')->nullable();
        $t->timestamps();
      });
    }
  }
  public function down(): void { Schema::dropIfExists('volunteer_profiles'); }
};
