<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_user_id');
                $table->string('name',191);
                $table->string('license_no',191)->nullable();
                $table->string('status',20)->default('active'); // active|pending|suspended
                $table->timestamps();
                $table->index('owner_user_id');
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('organizations');
    }
};
