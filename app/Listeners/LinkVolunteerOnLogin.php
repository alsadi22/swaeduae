<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LinkVolunteerOnLogin
{
    public function handle(Login $event): void
    {
        $uid = $event->user->id ?? null;
        if (!$uid) return;

        $exists = DB::table('volunteers')->where('user_id', $uid)->exists();
        if (!$exists) {
            DB::table('volunteers')->insert([
                'user_id' => $uid,
                'phone'   => '',
                'address' => '',
                'date_of_birth' => null,
                'interests' => null,
                'skills' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
