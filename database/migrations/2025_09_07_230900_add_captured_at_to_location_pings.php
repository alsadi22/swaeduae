<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('location_pings', 'captured_at')) {
            Schema::table('location_pings', function (Blueprint $table) {
                $table->timestamp('captured_at')->nullable()->after('accuracy');
            });
        }
    }
    public function down(): void {
        if (Schema::hasColumn('location_pings', 'captured_at')) {
            Schema::table('location_pings', function (Blueprint $table) {
                $table->dropColumn('captured_at');
            });
        }
    }
};
