<?php

namespace App\Policies;

use App\Models\ElectionVoter;
use App\Models\User;

class VoterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ElectionVoter $voter): bool
    {
        return $user->isAdmin() || $voter->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ElectionVoter $voter): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ElectionVoter $voter): bool
    {
        return $user->isAdmin();
    }
}
