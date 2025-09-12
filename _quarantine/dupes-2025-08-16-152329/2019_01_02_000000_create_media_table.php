<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $t) {
                $t->id();
                $t->string('disk')->default('public');
                $t->string('path');
                $t->string('original_name');
                $t->string('mime',128)->nullable();
                $t->unsignedBigInteger('size')->default(0);
                $t->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('media');
    }
};
