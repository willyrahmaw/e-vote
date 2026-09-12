<?php

namespace App\Livewire\Voter;

use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Queries\Election\GetActiveElections;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Portal Pemilihan Saya')]
class ElectionList extends Component
{
    public function render(GetActiveElections $query): View
    {
        /** @var User $user */
        $user = Auth::user();

        $activeElections = $query->execute($user, 12);

        // Fetch user voting participation map
        $voterStatuses = ElectionVoter::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('election_id');

        return view('livewire.voter.election-list', [
            'activeElections' => $activeElections,
            'voterStatuses' => $voterStatuses,
            'user' => $user,
        ]);
    }
}
