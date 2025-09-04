<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('date');
                $table->string('location')->nullable();
                $table->decimal('hours', 5, 2)->nullable();
                $table->timestamps();
            });
        } else {
            // No-op: table already exists. If needed, add missing columns here.
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('events', 'date')) {
                    $table->date('date')->after('description');
                }
                if (!Schema::hasColumn('events', 'location')) {
                    $table->string('location')->nullable()->after('date');
                }
                if (!Schema::hasColumn('events', 'hours')) {
                    $table->decimal('hours', 5, 2)->nullable()->after('location');
                }
                if (!Schema::hasColumn('events', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('events')) {
            Schema::drop('events');
        }
    }
};
