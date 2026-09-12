<?php

namespace App\Queries\Voting;

use App\Models\Election;
use App\Models\ElectionVoter;
use Illuminate\Support\Facades\Cache;

class GetLiveVotingStatistics
{
    public function __construct(
        private GetElectionResults $resultsQuery,
    ) {}

    public function execute(Election $election): array
    {
        // Cache result for 3 seconds to avoid DB overload on concurrent polling
        return Cache::remember("election-live-stats:{$election->id}", now()->addSeconds(3), function () use ($election) {
            $totalVoters = ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('is_eligible', true)
                ->count();

            $votedCount = ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('has_voted', true)
                ->count();

            $notVotedCount = max(0, $totalVoters - $votedCount);
            $turnoutPercentage = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 2) : 0;
            $totalBallots = $election->ballots()->count();

            $results = $this->resultsQuery->execute($election);

            return [
                'total_voters' => $totalVoters,
                'voted_count' => $votedCount,
                'not_voted_count' => $notVotedCount,
                'turnout_percentage' => $turnoutPercentage,
                'total_ballots' => $totalBallots,
                'is_active' => $election->isActive(),
                'status' => $election->status->value,
                'status_label' => $election->status->label(),
                'results' => $results,
                'updated_at' => now()->format('H:i:s'),
            ];
        });
    }
}
