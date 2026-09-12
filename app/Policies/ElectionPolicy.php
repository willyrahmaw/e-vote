<?php

namespace App\Policies;

use App\Enums\ResultVisibility;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;

class ElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Election $election = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $election) {
            return false;
        }

        if ($election->is_public) {
            return true;
        }

        return ElectionVoter::query()
            ->where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ?Election $election = null): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ?Election $election = null): bool
    {
        return $user->isAdmin();
    }

    public function manageCandidates(User $user, ?Election $election = null): bool
    {
        return $user->isAdmin();
    }

    public function manageVoters(User $user, ?Election $election = null): bool
    {
        return $user->isAdmin();
    }

    public function viewResults(User $user, Election $election): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return match ($election->result_visibility) {
            ResultVisibility::Hidden => false,
            ResultVisibility::Live => true,
            ResultVisibility::AfterElection => $election->isEnded() || now()->gt($election->end_at),
            ResultVisibility::AfterVote => ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('user_id', $user->id)
                ->where('has_voted', true)
                ->exists(),
        };
    }

    public function vote(User $user, Election $election): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $election->isActive()) {
            return false;
        }

        return ElectionVoter::query()
            ->where('election_id', $election->id)
            ->where('user_id', $user->id)
            ->where('is_eligible', true)
            ->where('has_voted', false)
            ->exists();
    }
}
