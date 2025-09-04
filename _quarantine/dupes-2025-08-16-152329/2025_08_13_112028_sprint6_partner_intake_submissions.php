<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('partner_intake_submissions')) {
            Schema::create('partner_intake_submissions', function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->string('org_name',190);
                $t->string('contact_name',190)->nullable();
                $t->string('email',190);
                $t->string('phone',64)->nullable();
                $t->string('website',190)->nullable();   // real website; honeypot uses a different field
                $t->string('emirate',64)->nullable();
                $t->text('message')->nullable();
                $t->string('ip',64)->nullable();
                $t->string('user_agent',255)->nullable();
                $t->boolean('is_bot')->default(false);
                $t->string('status',24)->default('new');  // new, reviewed, closed
                $t->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('partner_intake_submissions'); }
};
