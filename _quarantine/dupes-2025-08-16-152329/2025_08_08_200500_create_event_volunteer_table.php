<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('event_volunteer')) {
            Schema::create('event_volunteer', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->string('status', 20)->default('pending'); // pending|approved|rejected
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->unique(['event_id','user_id']);
                $table->index('status');
                // Foreign keys optional on shared hosting
                // $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
                // $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        } else {
            Schema::table('event_volunteer', function (Blueprint $table) {
                if (!Schema::hasColumn('event_volunteer','status'))      $table->string('status',20)->default('pending')->after('user_id');
                if (!Schema::hasColumn('event_volunteer','applied_at'))  $table->timestamp('applied_at')->nullable()->after('status');
                if (!Schema::hasColumn('event_volunteer','approved_at')) $table->timestamp('approved_at')->nullable()->after('applied_at');
                if (!Schema::hasColumn('event_volunteer','created_at'))  $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('event_volunteer');
    }
};
