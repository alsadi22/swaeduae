#!/usr/bin/env bash
set -euo pipefail

backup() { cp -a "$1" "$1.bak.$(date +%s)"; }

# 1) Canonicalize auth routes: /login serves the form, /org/login -> /login, /register disabled
backup routes/auth.php
cat > routes/auth.php <<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
| Canonical auth endpoints
*/
Route::middleware(['web','guest'])->group(function () {
    // Serve the login form directly (no redirect loops)
    Route::get('/login', function () { return view('auth.login'); })->name('login');

    // Existing login handler
    Route::post('/login', [AuthController::class,'login'])->name('login.post');

    // Keep legacy org login URL but redirect one-way to /login
    Route::get('/org/login', function () { return redirect('/login', 302); })->name('org.login');

    // Disable self-serve registration for now (avoid 500)
    Route::get('/register', function () { return redirect('/login', 302); })->name('register.disabled');
    Route::post('/register', function () { abort(404); });
});
PHP

# 2) Comment out conflicting routes in web.php and auth_public.php
for f in routes/web.php routes/auth_public.php; do
  [[ -f "$f" ]] || continue
  backup "$f"
  # Comment any '/login' -> '/org/login' redirection and duplicate '/org/login' definitions
  sed -i -E \
    -e "s#(^[[:space:]]*Route::get\('/login'.*redirect\('/org/login'[^;]*\);)#// \1#g" \
    -e "s#(^[[:space:]]*Route::middleware\(\[[^]]*\]\)->get\('/org/login'.*\);)#// \1#g" \
    -e "s#(^[[:space:]]*Route::get\('/org/login'.*\);)#// \1#g" \
    "$f"
done

# 3) Clear route/config cache so new routes take effect
php artisan route:clear
php artisan config:clear

# 4) Replace DemoDataSeeder with a column-safe version
backup database/seeders/DemoDataSeeder.php
cat > database/seeders/DemoDataSeeder.php <<'PHP'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // --- Users: owner & manager
        $ownerEmail = 'owner@swaeduae.ae';
        $managerEmail = 'manager@swaeduae.ae';
        $owner = DB::table('users')->where('email', $ownerEmail)->first();
        if (!$owner) {
            $ownerId = DB::table('users')->insertGetId([
                'name' => 'Demo Owner',
                'email' => $ownerEmail,
                'password' => Hash::make('Password123!'),
                'email_verified_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $owner = DB::table('users')->where('id', $ownerId)->first();
        }

        $manager = DB::table('users')->where('email', $managerEmail)->first();
        if (!$manager) {
            $managerId = DB::table('users')->insertGetId([
                'name' => 'Demo Manager',
                'email' => $managerEmail,
                'password' => Hash::make(Str::random(16)),
                'email_verified_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $manager = DB::table('users')->where('id', $managerId)->first();
        }

        // --- Organization (primary_color only if column exists)
        $org = DB::table('organizations')->where('owner_user_id', $owner->id)->first();
        if (!$org) {
            $row = [
                'name' => 'Demo Organization',
                'owner_user_id' => $owner->id,
                'created_at' => $now, 'updated_at' => $now,
            ];
            if (Schema::hasColumn('organizations','primary_color')) {
                $row['primary_color'] = '#0d6efd';
            }
            $orgId = DB::table('organizations')->insertGetId($row);
            $org = DB::table('organizations')->where('id', $orgId)->first();
        } else {
            $updates = ['updated_at' => $now];
            if (Schema::hasColumn('organizations','primary_color')) {
                $updates['primary_color'] = $org->primary_color ?: '#0d6efd';
            }
            DB::table('organizations')->where('id', $org->id)->update($updates);
        }

        // --- Team link
        if (Schema::hasTable('organization_users')) {
            DB::table('organization_users')->updateOrInsert(
                ['organization_id' => $org->id, 'user_id' => $manager->id],
                ['role' => 'org_manager', 'updated_at' => $now, 'created_at' => $now]
            );
        }

        // --- Volunteers
        $volIds = [];
        for ($i = 1; $i <= 15; $i++) {
            $email = sprintf('vol%02d@swaeduae.ae', $i);
            $u = DB::table('users')->where('email', $email)->first();
            if (!$u) {
                $id = DB::table('users')->insertGetId([
                    'name' => 'Volunteer '.$i,
                    'email' => $email,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => $now,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $u = DB::table('users')->where('id', $id)->first();
            }
            $volIds[] = $u->id;
        }

        // --- Helper: safe insert opportunity with only columns that exist
        $mkOpp = function (string $title, Carbon $start, Carbon $end, array $extra = []) use ($org, $now) {
            $row = [
                'organization_id' => $org->id,
                'title' => $title,
                'created_at' => $now, 'updated_at' => $now,
            ];
            if (Schema::hasColumn('opportunities', 'start_at')) $row['start_at'] = $start;
            if (Schema::hasColumn('opportunities', 'end_at'))   $row['end_at']   = $end;
            if (Schema::hasColumn('opportunities', 'status'))   $row['status']   = 'open';
            if (Schema::hasColumn('opportunities', 'capacity')) $row['capacity'] = 50;
            $row = array_merge($row, $extra);

            $existing = DB::table('opportunities')->where('organization_id', $org->id)->where('title', $title)->first();
            if ($existing) return $existing;
            $id = DB::table('opportunities')->insertGetId($row);
            return DB::table('opportunities')->where('id', $id)->first();
        };

        $now = Carbon::now();
        $oppPast     = $mkOpp('Community Cleanup (Past)', $now->copy()->subDays(7)->setTime(9,0),  $now->copy()->subDays(7)->setTime(12,0));
        $oppToday    = $mkOpp('Food Drive (Today)',       $now->copy()->setTime(9,0),              $now->copy()->setTime(15,0),
            ['geofence_lat' => 25.204849, 'geofence_lng' => 55.270783, 'geofence_radius_m' => 150]);
        $oppUpcoming = $mkOpp('Expo Support (Upcoming)',  $now->copy()->addDays(5)->setTime(10,0), $now->copy()->addDays(5)->setTime(16,0));

        // --- Applications + Attendances
        if (Schema::hasTable('applications')) {
            foreach (array_slice($volIds, 0, 10) as $idx => $uid) {
                $status = $idx < 6 ? 'approved' : ($idx < 8 ? 'pending' : 'waitlist');
                DB::table('applications')->updateOrInsert(
                    ['opportunity_id' => $oppToday->id, 'user_id' => $uid],
                    ['status' => $status, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        if (Schema::hasTable('attendances')) {
            $approved = array_slice($volIds, 0, 5);
            foreach ($approved as $i => $uid) {
                $in  = $now->copy()->setTime(9 + $i, 0);
                $out = $now->copy()->setTime(11 + $i, 30);
                $mins = max(1, $in->diffInMinutes($out));
                $insert = [
                    'opportunity_id' => $oppToday->id,
                    'user_id' => $uid,
                    'status' => 'present',
                    'check_in_at' => $in,
                    'check_out_at' => $out,
                    'minutes' => $mins,
                    'created_at' => $now, 'updated_at' => $now,
                ];
                if (Schema::hasColumn('attendances','check_in_lat')) {
                    $insert += ['check_in_lat'=>25.204800, 'check_in_lng'=>55.270700, 'check_in_acc'=>10.5];
                }
                if (Schema::hasColumn('attendances','check_out_lat')) {
                    $insert += ['check_out_lat'=>25.205000, 'check_out_lng'=>55.270900, 'check_out_acc'=>9.8];
                }
                $exists = DB::table('attendances')
                    ->where('opportunity_id', $oppToday->id)
                    ->where('user_id', $uid)->exists();
                if (!$exists) DB::table('attendances')->insert($insert);
            }
        }

        // --- KYC pending
        if (Schema::hasTable('org_kyc')) {
            DB::table('org_kyc')->updateOrInsert(
                ['organization_id' => $org->id],
                ['status'=>'pending', 'file_path'=>null, 'submitted_at'=>null, 'updated_at'=>$now, 'created_at'=>$now]
            );
        }

        echo "DemoDataSeeder: owner={$ownerEmail} pass=Password123! | org_id={$org->id}\n";
    }
}
PHP
