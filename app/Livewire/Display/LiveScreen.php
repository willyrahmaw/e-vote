<?php

namespace App\Livewire\Display;

use App\Models\Ballot;
use App\Models\Election;
use App\Queries\Voting\GetLiveVotingStatistics;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.base')]
class LiveScreen extends Component
{
    public ?string $electionId = null;
    public ?int $knownBallotCount = null;

    public function mount(?string $election = null): void
    {
        if ($election) {
            $found = Election::query()
                ->where('id', $election)
                ->orWhere('slug', $election)
                ->first();

            $this->electionId = $found?->id;
        }

        if ($this->electionId) {
            $this->knownBallotCount = Ballot::where('election_id', $this->electionId)->count();
        }
    }

    public function selectElection(?string $id = null): void
    {
        $this->electionId = ! empty($id) ? $id : null;
        $this->knownBallotCount = null;
    }

    public function render(GetLiveVotingStatistics $statsQuery)
    {
        $election = $this->electionId
            ? Election::find($this->electionId)
            : null;

        $stats = $election ? $statsQuery->execute($election) : null;

        if ($election && $stats) {
            $currentCount = (int) ($stats['total_ballots'] ?? 0);

            if ($this->knownBallotCount !== null && $currentCount > $this->knownBallotCount) {
                $diff = min(5, $currentCount - $this->knownBallotCount);
                $newBallots = Ballot::query()
                    ->where('election_id', $election->id)
                    ->latest('submitted_at')
                    ->take($diff)
                    ->get();

                foreach ($newBallots as $b) {
                    $maskedUuid = substr($b->id, 0, 8) . '•••' . substr($b->id, -4);
                    $this->dispatch('new-vote-received', [
                        'uuid' => $maskedUuid,
                        'time' => $b->submitted_at?->format('H:i:s') ?? now()->format('H:i:s'),
                    ]);
                }
            }

            $this->knownBallotCount = $currentCount;
        }

        $allElections = Election::query()
            ->where('status', '!=', \App\Enums\ElectionStatus::Draft)
            ->with(['candidateGroups', 'positions'])
            ->withCount(['candidateGroups', 'positions', 'ballots', 'voters'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.display.live-screen', [
            'election' => $election,
            'stats' => $stats,
            'allElections' => $allElections,
            'title' => $election ? "Live Monitor: {$election->name}" : 'Live Voting Screen',
        ]);
    }
}
