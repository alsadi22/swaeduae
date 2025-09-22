<?php
/**
 * Early auth/gate overrides loaded by routes/web.php
 * Keeps logic out of controllers and avoids provider edits.
 */
use Illuminate\Support\Facades\Gate;

if (! \function_exists('swaed_define_gates')) {
    function swaed_define_gates(): void {
        // Only define once
        if (!Gate::has('isAdmin')) {
            Gate::define('isAdmin', function ($user) {
                if (!$user) return false;
                // Accept either flag or role
                return (int)($user->is_admin ?? 0) === 1 || ($user->role ?? null) === 'admin';
            });
        }
    }
}
swaed_define_gates();
