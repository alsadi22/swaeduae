<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('volunteer_hours')) {
            Schema::create('volunteer_hours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
                $table->date('date');
                $table->decimal('hours', 5, 2)->default(0);
                $table->string('status')->default('pending'); // pending/approved/rejected
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('volunteer_hours', function (Blueprint $table) {
                if (!Schema::hasColumn('volunteer_hours', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
                }
                if (!Schema::hasColumn('volunteer_hours', 'event_id')) {
                    $table->foreignId('event_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
                }
                if (!Schema::hasColumn('volunteer_hours', 'date')) {
                    $table->date('date')->after('event_id');
                }
                if (!Schema::hasColumn('volunteer_hours', 'hours')) {
                    $table->decimal('hours', 5, 2)->default(0)->after('date');
                }
                if (!Schema::hasColumn('volunteer_hours', 'status')) {
                    $table->string('status')->default('pending')->after('hours');
                }
                if (!Schema::hasColumn('volunteer_hours', 'notes')) {
                    $table->string('notes')->nullable()->after('status');
                }
                if (!Schema::hasColumn('volunteer_hours', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('volunteer_hours')) {
            Schema::drop('volunteer_hours');
        }
    }
};
