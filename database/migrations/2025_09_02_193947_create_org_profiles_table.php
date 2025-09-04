<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('org_profiles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->index();
            $t->string('org_name');
            $t->string('emirate')->nullable();
            $t->string('phone')->nullable();
            $t->string('website')->nullable();
            $t->text('about')->nullable();
            $t->enum('status', ['pending','approved','rejected'])->default('pending');
            $t->timestamp('approved_at')->nullable();
            $t->text('admin_notes')->nullable();
            $t->timestamps();

            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('org_profiles');
    }
};
