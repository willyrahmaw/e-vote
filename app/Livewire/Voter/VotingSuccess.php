<?php

namespace App\Livewire\Voter;

use App\Models\Election;
use App\Models\ElectionVoter;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Suara Terkirim')]
class VotingSuccess extends Component
{
    public Election $election;
    public string $refCode = '';

    public function mount(Election $election): void
    {
        $this->election = $election;
        $this->refCode = request()->query('ref', 'VOTE-' . strtoupper(substr(md5((string) microtime(true)), 0, 10)));

        $userId = Auth::id();

        // Ensure voter has actually voted
        $voter = ElectionVoter::query()
            ->where('election_id', $election->id)
            ->where('user_id', $userId)
            ->where('has_voted', true)
            ->first();

        if (! $voter) {
            $this->redirectRoute('voter.dashboard');
        }
    }

    public function render(): View
    {
        return view('livewire.voter.voting-success', [
            'election' => $this->election,
            'refCode' => $this->refCode,
        ]);
    }
}
