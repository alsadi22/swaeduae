<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only create the stories table if it doesn't already exist
        if (!Schema::hasTable('stories')) {
            Schema::create('stories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('body');
                $table->string('cover_image')->nullable();
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Drop the stories table only if it exists
        if (Schema::hasTable('stories')) {
            Schema::dropIfExists('stories');
        }
    }
};
