<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->string('location')->nullable();
            $table->string('region')->nullable(); // e.g., Sharjah, Dubai
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('slots')->default(0);
            $table->text('requirements')->nullable();
            $table->string('status')->default('open'); // open, closed, archived
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('opportunities');
    }
};
