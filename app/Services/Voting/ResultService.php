<?php

namespace App\Services\Voting;

use App\Enums\ResultVisibility;
use App\Models\Election;
use App\Models\User;
use App\Queries\Voting\GetElectionResults;

class ResultService
{
    public function __construct(
        private GetElectionResults $resultsQuery,
    ) {}

    public function getResults(Election $election): array
    {
        return $this->resultsQuery->execute($election);
    }

    public function canUserViewResults(?User $user, Election $election): bool
    {
        if (! $user) {
            return $election->result_visibility === ResultVisibility::Live;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return match ($election->result_visibility) {
            ResultVisibility::Hidden => false,
            ResultVisibility::Live => true,
            ResultVisibility::AfterElection => $election->isEnded() || now()->gt($election->end_at),
            ResultVisibility::AfterVote => $election->voters()
                ->where('user_id', $user->id)
                ->where('has_voted', true)
                ->exists(),
        };
    }
}
