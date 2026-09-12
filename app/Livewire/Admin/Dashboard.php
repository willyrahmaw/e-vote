<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use App\Queries\Election\GetElectionStatistics;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public function render(GetElectionStatistics $statsQuery): View
    {
        $stats = $statsQuery->execute();
        $recentElections = Election::query()
            ->withCount(['voters', 'ballots'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recentElections' => $recentElections,
        ]);
    }
}
