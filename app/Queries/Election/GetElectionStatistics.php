<?php

namespace App\Queries\Election;

use App\Enums\ElectionStatus;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;

class GetElectionStatistics
{
    public function execute(): array
    {
        $totalElections = Election::count();
        $activeElections = Election::where('status', ElectionStatus::Active)->count();
        $scheduledElections = Election::where('status', ElectionStatus::Scheduled)->count();
        $endedElections = Election::where('status', ElectionStatus::Ended)->count();
        $totalCandidates = Candidate::count();
        $totalUsers = User::count();
        $totalVotersRegistered = ElectionVoter::count();
        $totalVotesCast = ElectionVoter::where('has_voted', true)->count();

        $turnoutRate = $totalVotersRegistered > 0
            ? round(($totalVotesCast / $totalVotersRegistered) * 100, 2)
            : 0;

        return [
            'total_elections' => $totalElections,
            'active_elections' => $activeElections,
            'scheduled_elections' => $scheduledElections,
            'ended_elections' => $endedElections,
            'total_candidates' => $totalCandidates,
            'total_users' => $totalUsers,
            'total_voters_registered' => $totalVotersRegistered,
            'total_votes_cast' => $totalVotesCast,
            'turnout_rate' => $turnoutRate,
        ];
    }
}
