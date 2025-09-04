<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('event_id')->nullable();
                $table->string('certificate_code')->unique();
                $table->string('status')->default('issued'); // issued, revoked
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
