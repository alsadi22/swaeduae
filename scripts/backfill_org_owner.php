<?php
// Usage: php scripts/backfill_org_owner.php hamad
//        php scripts/backfill_org_owner.php 123   (numeric user id)

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$needle = $argv[1] ?? null;
if (!$needle) {
    fwrite(STDERR, "Usage: php scripts/backfill_org_owner.php <user_id|name|email-fragment>\n");
    exit(1);
}

$user = null;
if (ctype_digit((string)$needle)) {
    $user = DB::table('users')->where('id', (int)$needle)->first();
} else {
    $candidates = DB::table('users')
        ->where('email', 'like', "%{$needle}%")
        ->orWhere('name', 'like', "%{$needle}%")
        ->get();

    if ($candidates->count() === 0) {
        fwrite(STDERR, "No users matched '{$needle}'.\n");
        exit(2);
    }
    if ($candidates->count() > 1) {
        echo "Multiple users matched '{$needle}':\n";
        foreach ($candidates as $c) {
            echo " - id={$c->id} name='{$c->name}' email='{$c->email}'\n";
        }
        echo "Re-run with the exact user id.\n";
        exit(3);
    }
    $user = $candidates->first();
}

if (!$user) {
    fwrite(STDERR, "User not found.\n");
    exit(4);
}
$uid = (int)$user->id;
echo "[*] Using user id={$uid} name='{$user->name}' email='{$user->email}'\n";

if (!Schema::hasTable('organizations')) {
    fwrite(STDERR, "organizations table does not exist.\n");
    exit(5);
}
if (!Schema::hasColumn('organizations', 'owner_user_id')) {
    fwrite(STDERR, "organizations.owner_user_id column does not exist (run the migration first).\n");
    exit(6);
}

$totalBefore = DB::table('organizations')->count();
$nullBefore  = DB::table('organizations')->whereNull('owner_user_id')->count();
echo "[*] organizations: total={$totalBefore}, owner_user_id NULL={$nullBefore}\n";

$updated = 0;

DB::beginTransaction();
try {
    // Preferred: if there is a clear ownership column, fill using that.
    if (Schema::hasColumn('organizations', 'owner_id')) {
        $n = DB::table('organizations')
            ->whereNull('owner_user_id')
            ->where('owner_id', $uid)
            ->update(['owner_user_id' => $uid, 'updated_at' => now()]);
        if ($n) echo "[*] Set owner_user_id from owner_id match: {$n}\n";
        $updated += $n;
    }
    if (Schema::hasColumn('organizations', 'user_id')) {
        $n = DB::table('organizations')
            ->whereNull('owner_user_id')
            ->where('user_id', $uid)
            ->update(['owner_user_id' => $uid, 'updated_at' => now()]);
        if ($n) echo "[*] Set owner_user_id from user_id match: {$n}\n";
        $updated += $n;
    }

    // Fallback: if nothing updated yet and there is only one org with NULL owner_user_id, claim it.
    if ($updated === 0) {
        $nullRows = DB::table('organizations')->whereNull('owner_user_id')->get();
        if ($nullRows->count() === 1) {
            $orgId = $nullRows->first()->id;
            $n = DB::table('organizations')->where('id', $orgId)
                ->update(['owner_user_id' => $uid, 'updated_at' => now()]);
            if ($n) {
                echo "[*] Set owner_user_id for org id={$orgId} (single NULL row): {$n}\n";
                $updated += $n;
            }
        } elseif ($nullRows->count() > 1) {
            echo "[!] Multiple orgs have NULL owner_user_id. Skipping blanket assignment for safety.\n";
        }
    }

    // As a last resort (explicit), uncomment to fill ALL NULLs to this user (not recommended):
    // $updated += DB::table('organizations')->whereNull('owner_user_id')->update(['owner_user_id' => $uid, 'updated_at' => now()]);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    fwrite(STDERR, "Error: ".$e->getMessage()."\n");
    exit(7);
}

$nullAfter = DB::table('organizations')->whereNull('owner_user_id')->count();
echo "[*] Updated rows: {$updated}. Remaining NULL owner_user_id: {$nullAfter}\n";
echo "[✓] Done.\n";
