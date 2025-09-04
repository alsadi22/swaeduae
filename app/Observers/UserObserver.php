<?php
namespace App\Observers;
use App\Models\User;

class UserObserver {
    public function created(User $user){
        if (empty($user->volunteer_code)) {
            $user->volunteer_code = 'SV' . str_pad((string)$user->id, 7, '0', STR_PAD_LEFT);
            $user->saveQuietly();
        }
    }
}
