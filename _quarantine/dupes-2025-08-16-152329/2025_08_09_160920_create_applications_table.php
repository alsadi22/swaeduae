<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('opportunity_id');
                $table->enum('status', ['pending','approved','rejected'])->default('pending');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
