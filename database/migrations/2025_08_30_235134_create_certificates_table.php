<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('opportunity_id')->nullable()->index();
                $table->unsignedBigInteger('event_id')->nullable()->index();
                $table->string('code', 64)->unique();
                $table->string('signature', 64)->index();
                $table->string('pdf_path', 255)->nullable();
                $table->decimal('hours', 5, 2)->default(0);
                $table->timestamp('issued_at')->nullable()->index();
                $table->timestamp('revoked_at')->nullable()->index();
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('certificates');
    }
};
