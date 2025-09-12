<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class VolunteerHoursEdgeTest extends TestCase
{
    use RefreshDatabase;

    protected function mkUser()  { return \App\Models\User::factory()->create(); }
    protected function mkOpp()   { return \App\Models\Opportunity::factory()->create(); }

    /** per-row cap at 960 minutes (16h) */
    public function test_cap_per_row_to_960(): void
    {
        $u = $this->mkUser(); $o = $this->mkOpp();
        $base = now()->startOfMinute();

        \App\Models\Attendance::create([
            'user_id'=>$u->id,'opportunity_id'=>$o->id,
            'checkin_at'=>$base->copy()->subHours(20),
            'checkout_at'=>$base, 'token'=>\Str::uuid(),
        ]);

        \Artisan::call('hours:reconcile');

        $vh = \App\Models\VolunteerHour::whereUserId($u->id)->whereOpportunityId($o->id)->first();
        $this->assertSame(960, (int)$vh->minutes);
    }

    /** negative durations clamp to 0 */
    public function test_negative_duration_clamps_to_zero(): void
    {
        $u = $this->mkUser(); $o = $this->mkOpp();
        $base = now()->startOfMinute();

        \App\Models\Attendance::create([
            'user_id'=>$u->id,'opportunity_id'=>$o->id,
            'checkin_at'=>$base, 'checkout_at'=>$base->copy()->subHour(), 'token'=>\Str::uuid(),
        ]);

        \Artisan::call('hours:reconcile');

        $vh = \App\Models\VolunteerHour::whereUserId($u->id)->whereOpportunityId($o->id)->first();
        $this->assertSame(0, (int)($vh->minutes ?? 0));
    }

    /** minutes-only rows (no timestamps) get summed */
    public function test_minutes_only_rows_are_counted(): void
    {
        $u = $this->mkUser(); $o = $this->mkOpp();

        // ensure minutes column exists (guarded across envs)
        if (!Schema::hasColumn('attendances','minutes')) {
            Schema::table('attendances', fn(Blueprint $t) => $t->integer('minutes')->nullable());
        }

        \App\Models\Attendance::create([
            'user_id'=>$u->id,'opportunity_id'=>$o->id,'minutes'=>45,'token'=>\Str::uuid(),
        ]);
        \App\Models\Attendance::create([
            'user_id'=>$u->id,'opportunity_id'=>$o->id,'minutes'=>15,'token'=>\Str::uuid(),
        ]);

        \Artisan::call('hours:reconcile');

        $vh = \App\Models\VolunteerHour::whereUserId($u->id)->whereOpportunityId($o->id)->first();
        $this->assertSame(60, (int)$vh->minutes);
    }

    /** alternate column names (check_in_at / check_out_at) are detected */
    public function test_alternate_timestamp_columns(): void
    {
        // add alt columns if missing
        if (!Schema::hasColumn('attendances','check_in_at') || !Schema::hasColumn('attendances','check_out_at')) {
            Schema::table('attendances', function(Blueprint $t){
                if (!Schema::hasColumn('attendances','check_in_at'))  $t->timestamp('check_in_at')->nullable();
                if (!Schema::hasColumn('attendances','check_out_at')) $t->timestamp('check_out_at')->nullable();
            });
        }

        $u = $this->mkUser(); $o = $this->mkOpp();
        $base = now()->startOfMinute();

        \DB::table('attendances')->insert([
            'user_id'=>$u->id,
            'opportunity_id'=>$o->id,
            'check_in_at'=>$base->copy()->subHours(2),
            'check_out_at'=>$base,
            'token'=>(string)\Str::uuid(),
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        \Artisan::call('hours:reconcile');

        $vh = \App\Models\VolunteerHour::whereUserId($u->id)->whereOpportunityId($o->id)->first();
        $this->assertSame(120, (int)$vh->minutes);
    }
}
