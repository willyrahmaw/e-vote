<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use App\Queries\Voting\GetLiveVotingStatistics;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Live Voting Realtime Monitor')]
class LiveVotingDashboard extends Component
{
    public ?string $selectedElectionId = null;

    public function mount(?string $electionId = null): void
    {
        $this->selectedElectionId = $electionId ?? Election::where('status', \App\Enums\ElectionStatus::Active)->value('id') ?? Election::latest()->value('id');
    }

    public function selectElection(string $id): void
    {
        $this->selectedElectionId = $id;
    }

    public function render(GetLiveVotingStatistics $statsQuery): View
    {
        $elections = Election::orderBy('start_at', 'desc')->get();
        $currentElection = $this->selectedElectionId ? Election::find($this->selectedElectionId) : null;

        $stats = $currentElection ? $statsQuery->execute($currentElection) : null;

        return view('livewire.admin.live-voting-dashboard', [
            'elections' => $elections,
            'currentElection' => $currentElection,
            'stats' => $stats,
        ]);
    }
}
