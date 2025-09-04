<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('events') && !Schema::hasColumn('events','slug')) {
            Schema::table('events', function (Blueprint $t) {
                $t->string('slug', 200)->nullable()->after('title');
            });

            // Backfill unique slugs: slug(title) or fallback "event-{id}"
            $rows = DB::table('events')->select('id','title')->orderBy('id')->get();
            foreach ($rows as $r) {
                $base = Str::slug($r->title ?? '') ?: ('event-'.$r->id);
                $slug = $base; $i = 2;
                while (DB::table('events')->where('id','!=',$r->id)->where('slug',$slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                DB::table('events')->where('id',$r->id)->update(['slug'=>$slug]);
            }

            // Add unique index
            Schema::table('events', function (Blueprint $t) {
                $t->unique('slug');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('events') && Schema::hasColumn('events','slug')) {
            Schema::table('events', function (Blueprint $t) {
                $t->dropUnique(['slug']);
                $t->dropColumn('slug');
            });
        }
    }
};
