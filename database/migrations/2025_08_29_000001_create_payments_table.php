<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->nullableMorphs('payable');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('provider')->default('stripe');
                $table->string('currency', 10)->default('AED');
                $table->unsignedBigInteger('amount');
                $table->string('status')->default('pending');
                $table->string('provider_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['provider','provider_id']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};

