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
        $ownerEmail   = 'owner@swaeduae.ae';
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

        // --- Organization (schema-agnostic)
        $org = null;
        if ($this->tableExists('organizations')) {
            $orgOwnerFk = $this->fk('organizations', ['owner_user_id','owner_id','user_id']);
            $q = DB::table('organizations')->where('name', 'Demo Organization');
            if ($orgOwnerFk) $q->where($orgOwnerFk, $owner->id);
            $org = $q->first();

            if (!$org) {
                $row = [
                    'name' => 'Demo Organization',
                    'created_at' => $now, 'updated_at' => $now,
                ];
                if ($orgOwnerFk) $row[$orgOwnerFk] = $owner->id;
                if (Schema::hasColumn('organizations','primary_color')) $row['primary_color'] = '#0d6efd';
                $id = DB::table('organizations')->insertGetId($this->filterToExisting('organizations', $row));
                $org = DB::table('organizations')->where('id',$id)->first();
            } else {
                if (Schema::hasColumn('organizations','primary_color') && empty($org->primary_color)) {
                    DB::table('organizations')->where('id',$org->id)->update(['primary_color'=>'#0d6efd','updated_at'=>$now]);
                }
            }
        }

        // --- Join manager to org
        if ($org && $this->tableExists('organization_users')) {
            $ouOrgFk  = $this->fk('organization_users', ['organization_id','org_id','company_id','tenant_id']);
            $ouUserFk = $this->fk('organization_users', ['user_id','member_id']);
            if ($ouOrgFk && $ouUserFk) {
                $row = [
                    $ouOrgFk  => $org->id,
                    $ouUserFk => $manager->id,
                    'created_at' => $now, 'updated_at' => $now,
                ];
                if (Schema::hasColumn('organization_users','role')) $row['role'] = 'org_manager';
                DB::table('organization_users')->updateOrInsert(
                    [$ouOrgFk => $org->id, $ouUserFk => $manager->id],
                    $this->filterToExisting('organization_users', $row)
                );
            }
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

        // --- Opportunities
        $oppToday = null;
        if ($this->tableExists('opportunities')) {
            $oppOrgFk = $this->fk('opportunities', ['organization_id','org_id','company_id','tenant_id','owner_id']);

            $mkOpp = function (string $title, Carbon $start, Carbon $end, array $extra = []) use ($org, $oppOrgFk, $now) {
                $base = [
                    'title'      => $title,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($oppOrgFk && $org) $base[$oppOrgFk] = $org->id;

                if (Schema::hasColumn('opportunities','start_at'))  $base['start_at']  = $start;
                if (Schema::hasColumn('opportunities','end_at'))    $base['end_at']    = $end;
                if (Schema::hasColumn('opportunities','starts_at')) $base['starts_at'] = $start;
                if (Schema::hasColumn('opportunities','ends_at'))   $base['ends_at']   = $end;

                if (Schema::hasColumn('opportunities','description'))       $base['description']       = 'Demo opportunity seeded for testing.';
                if (Schema::hasColumn('opportunities','short_description')) $base['short_description'] = 'Demo opportunity';
                if (Schema::hasColumn('opportunities','status'))            $base['status']            = 'open';
                if (Schema::hasColumn('opportunities','capacity'))          $base['capacity']          = 50;
                if (Schema::hasColumn('opportunities','slug'))              $base['slug']              = Str::slug($title).'-'.substr(md5($title.$now),0,6);
                if (Schema::hasColumn('opportunities','is_published'))      $base['is_published']      = 1;
                if (Schema::hasColumn('opportunities','published'))         $base['published']         = 1;
                if (Schema::hasColumn('opportunities','published_at'))      $base['published_at']      = $now;

                $row = array_merge($base, $extra);

                $q = DB::table('opportunities')->where('title', $title);
                if ($oppOrgFk && $org) $q->where($oppOrgFk, $org->id);
                $existing = $q->first();
                if ($existing) return $existing;

                $id = DB::table('opportunities')->insertGetId($this->filterToExisting('opportunities', $row));
                return DB::table('opportunities')->where('id', $id)->first();
            };

            $mkOpp('Community Cleanup (Past)',  $now->copy()->subDays(7)->setTime(9,0),  $now->copy()->subDays(7)->setTime(12,0));
            $oppToday = $mkOpp('Food Drive (Today)', $now->copy()->setTime(9,0), $now->copy()->setTime(15,0),
                ['geofence_lat'=>25.204849,'geofence_lng'=>55.270783,'geofence_radius_m'=>150]);
            $mkOpp('Expo Support (Upcoming)',   $now->copy()->addDays(5)->setTime(10,0), $now->copy()->addDays(5)->setTime(16,0));
        }

        // --- Applications (STATUS-AWARE)
        if ($oppToday && $this->tableExists('applications')) {
            $appOppFk  = $this->fk('applications', ['opportunity_id','event_id','job_id']);
            $appUserFk = $this->fk('applications', ['user_id','volunteer_id','member_id']);

            if ($appOppFk && $appUserFk) {
                $statusCol = Schema::hasColumn('applications','status') ? 'status' : null;
                $statusType = $statusCol ? $this->columnBaseType('applications', $statusCol) : null;
                $allowedStatuses = ($statusCol && $statusType === 'enum')
                    ? $this->enumAllowedValues('applications', $statusCol)
                    : [];

                $mapDesiredToAllowed = function (string $desired) use ($allowedStatuses, $statusType) {
                    // Numeric-like? safest is to skip setting.
                    if ($statusType === 'int') return null;

                    // Free-text string? return desired.
                    if ($statusType === 'string') return $desired;

                    // Enum: map synonyms
                    if ($statusType === 'enum' && !empty($allowedStatuses)) {
                        $synonyms = [
                            'approved' => ['approved','accept','accepted','confirm','confirmed','approved'],
                            'pending'  => ['pending','in_review','applied','new','submitted'],
                            'waitlist' => ['waitlist','waitlisted','reserve','on_hold','backup','reserved'],
                        ];
                        $want = isset($synonyms[$desired]) ? $synonyms[$desired] : [$desired];
                        $allowed = array_map('strtolower', $allowedStatuses);
                        foreach ($want as $w) {
                            if (in_array(strtolower($w), $allowed, true)) return $w;
                        }
                        // fallback if 'pending' exists, otherwise first allowed
                        if (in_array('pending', $allowed, true)) return 'pending';
                        return $allowedStatuses[0];
                    }

                    // Unknown type: safest is to not set
                    return null;
                };

                foreach (array_slice($volIds, 0, 10) as $idx => $uid) {
                    $desired = $idx < 6 ? 'approved' : ($idx < 8 ? 'pending' : 'waitlist');

                    $row = [
                        $appOppFk => $oppToday->id,
                        $appUserFk => $uid,
                        'created_at' => $now, 'updated_at' => $now,
                    ];

                    if ($statusCol) {
                        $mapped = $mapDesiredToAllowed($desired);
                        if ($mapped !== null) $row[$statusCol] = $mapped;
                    }

                    DB::table('applications')->updateOrInsert(
                        [$appOppFk => $oppToday->id, $appUserFk => $uid],
                        $this->filterToExisting('applications', $row)
                    );
                }
            }
        }

        // --- Attendances
        if ($oppToday && $this->tableExists('attendances')) {
            $attOppFk  = $this->fk('attendances', ['opportunity_id','event_id','job_id']);
            $attUserFk = $this->fk('attendances', ['user_id','volunteer_id','member_id']);
            if ($attOppFk && $attUserFk) {
                $approved = array_slice($volIds, 0, 5);
                foreach ($approved as $i => $uid) {
                    $in  = $now->copy()->setTime(9 + $i, 0);
                    $out = $now->copy()->setTime(11 + $i, 30);
                    $mins = max(1, $in->diffInMinutes($out));

                    $insert = [
                        $attOppFk   => $oppToday->id,
                        $attUserFk  => $uid,
                        'created_at'=> $now, 'updated_at'=> $now,
                    ];
                    if (Schema::hasColumn('attendances','status'))        $insert['status'] = 'present';
                    if (Schema::hasColumn('attendances','minutes'))       $insert['minutes'] = $mins;

                    foreach (['check_in_at','checked_in_at','in_at','clock_in_at'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = $in; break; }
                    foreach (['check_out_at','checked_out_at','out_at','clock_out_at'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = $out; break; }

                    foreach (['check_in_lat','checkin_lat','in_lat'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = 25.204800; break; }
                    foreach (['check_in_lng','checkin_lng','in_lng'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = 55.270700; break; }
                    foreach (['check_out_lat','checkout_lat','out_lat'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = 25.205000; break; }
                    foreach (['check_out_lng','checkout_lng','out_lng'] as $col)
                        if (Schema::hasColumn('attendances',$col)) { $insert[$col] = 55.270900; break; }

                    $exists = DB::table('attendances')
                        ->where($attOppFk, $oppToday->id)
                        ->where($attUserFk, $uid)->exists();
                    if (!$exists) {
                        DB::table('attendances')->insert($this->filterToExisting('attendances', $insert));
                    }
                }
            }
        }

        // --- Org KYC
        if ($org && $this->tableExists('org_kyc')) {
            $kycOrgFk = $this->fk('org_kyc', ['organization_id','org_id','company_id','tenant_id']);
            $row = [
                'status' => 'pending',
                'updated_at' => $now, 'created_at' => $now,
            ];
            if ($kycOrgFk) $row[$kycOrgFk] = $org->id;

            DB::table('org_kyc')->updateOrInsert(
                $kycOrgFk ? [$kycOrgFk => $org->id] : ['status'=>'pending'],
                $this->filterToExisting('org_kyc', $row)
            );
        }

        echo "DemoDataSeeder: owner={$ownerEmail} pass=Password123!" . ($org ? " | org_id={$org->id}" : "") . PHP_EOL;
    }

    /* ----------------- Helpers ----------------- */

    private function tableExists(string $table): bool
    {
        try { return Schema::hasTable($table); } catch (\Throwable $e) { return false; }
    }

    private function fk(string $table, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            try { if (Schema::hasColumn($table, $c)) return $c; } catch (\Throwable $e) {}
        }
        return null;
    }

    private function filterToExisting(string $table, array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            try { if (Schema::hasColumn($table, $k)) $out[$k] = $v; } catch (\Throwable $e) {}
        }
        return $out;
    }

    private function columnBaseType(string $table, string $column): ?string
    {
        try {
            $rows = DB::select("SHOW COLUMNS FROM `{$table}` LIKE ?", [$column]);
            if (!$rows) return null;
            $type = strtolower($rows[0]->Type ?? '');
            if (strpos($type, 'enum(') === 0) return 'enum';
            if (strpos($type, 'int') !== false) return 'int';
            if (strpos($type, 'char') !== false || strpos($type, 'text') !== false) return 'string';
            return 'other';
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function enumAllowedValues(string $table, string $column): array
    {
        try {
            $rows = DB::select("SHOW COLUMNS FROM `{$table}` LIKE ?", [$column]);
            if (!$rows) return [];
            $type = strtolower($rows[0]->Type ?? '');
            if (strpos($type, 'enum(') !== 0) return [];
            if (!preg_match('/^enum\((.*)\)$/', $type, $m)) return [];
            $raw = $m[1]; // e.g. 'pending','accepted','rejected'
            $vals = str_getcsv($raw, ',', "'", "\\");
            return array_values(array_unique(array_map('strtolower', $vals)));
        } catch (\Throwable $e) {
            return [];
        }
    }
}
