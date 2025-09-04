<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create the opportunities table only if it doesn't already exist
        if (!Schema::hasTable('opportunities')) {
            Schema::create('opportunities', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->dateTime('start_date');
                $table->dateTime('end_date')->nullable();
                $table->string('location')->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_virtual')->default(false);
                $table->integer('volunteers_required')->nullable();
                $table->integer('volunteers_count')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Only drop the table if it exists
        if (Schema::hasTable('opportunities')) {
            Schema::dropIfExists('opportunities');
        }
    }
};
