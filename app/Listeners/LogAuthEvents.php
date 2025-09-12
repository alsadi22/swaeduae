<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;

class LogAuthEvents
{
    public function handle($event): void
    {
        if ($event instanceof Login) {
            DB::table('audit_logs')->insert([
                'user_id'    => $event->user->id ?? null,
                'action'     => 'login.success',
                'ip_address' => request()->ip(),
                'meta'       => json_encode(['email' => $event->user->email ?? null]),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } elseif ($event instanceof Failed) {
            DB::table('audit_logs')->insert([
                'user_id'    => null,
                'action'     => 'login.failed',
                'ip_address' => request()->ip(),
                'meta'       => json_encode(['email' => $event->credentials['email'] ?? null]),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
