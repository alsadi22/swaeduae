<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only create the partners table if it doesn't already exist
        if (!Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('website')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Drop the partners table only if it exists
        if (Schema::hasTable('partners')) {
            Schema::dropIfExists('partners');
        }
    }
};
