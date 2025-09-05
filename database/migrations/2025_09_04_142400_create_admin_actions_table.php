<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');                     // approve | deny | etc.
            $table->string('subject_type');               // org | application
            $table->unsignedBigInteger('subject_id');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['subject_type','subject_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('admin_actions');
    }
};
