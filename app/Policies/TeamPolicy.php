<?php
namespace App\Policies;

use App\Models\User;

class TeamPolicy
{
    // Keep minimal logic; deny by default to be safe.
    public function viewAny(?User $user): bool { return true; }
    public function view(?User $user, $team): bool { return true; }
    public function create(User $user): bool { return false; }
    public function update(User $user, $team): bool { return false; }
    public function delete(User $user, $team): bool { return false; }
    public function restore(User $user, $team): bool { return false; }
    public function forceDelete(User $user, $team): bool { return false; }
}
