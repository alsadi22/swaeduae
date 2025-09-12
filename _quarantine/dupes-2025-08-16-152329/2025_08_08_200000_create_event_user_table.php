<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('event_user')) {
            Schema::create('event_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('joined_at')->nullable();
                $table->unique(['event_id','user_id']);
                $table->timestamps();
            });
        } else {
            Schema::table('event_user', function (Blueprint $table) {
                if (!Schema::hasColumn('event_user','joined_at')) {
                    $table->timestamp('joined_at')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('event_user','created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('event_user')) {
            Schema::drop('event_user');
        }
    }
};
