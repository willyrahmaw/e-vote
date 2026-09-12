<?php

namespace App\Queries\Voter;

use App\Models\Election;
use App\Models\ElectionVoter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class GetElectionVoters
{
    public function execute(Election $election, ?string $search = null, ?string $filterStatus = null, int $perPage = 15): LengthAwarePaginator
    {
        return ElectionVoter::query()
            ->with(['user'])
            ->where('election_id', $election->id)
            ->when($search, function (Builder $query, string $search) {
                $query->whereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->when($filterStatus === 'voted', fn (Builder $q) => $q->where('has_voted', true))
            ->when($filterStatus === 'not_voted', fn (Builder $q) => $q->where('has_voted', false))
            ->when($filterStatus === 'eligible', fn (Builder $q) => $q->where('is_eligible', true))
            ->when($filterStatus === 'ineligible', fn (Builder $q) => $q->where('is_eligible', false))
            ->latest()
            ->paginate($perPage);
    }
}
