<?php

namespace App\Livewire\Voter;

use App\Models\Election;
use App\Models\User;
use App\Queries\Voting\GetLiveVotingStatistics;
use App\Services\Voting\ResultService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Hasil Pemilihan')]
class LiveResults extends Component
{
    public Election $election;

    public function mount(Election $election, ResultService $resultService): void
    {
        $this->election = $election;

        /** @var User|null $user */
        $user = Auth::user();

        if (! $resultService->canUserViewResults($user, $this->election)) {
            abort(403, 'Hasil pemilihan belum dapat dilihat sesuai pengaturan keterbukaan hasil.');
        }
    }

    public function render(GetLiveVotingStatistics $statsQuery): View
    {
        $stats = $statsQuery->execute($this->election);

        return view('livewire.voter.live-results', [
            'election' => $this->election,
            'stats' => $stats,
        ]);
    }
}
