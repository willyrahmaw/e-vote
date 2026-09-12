<?php

namespace App\Queries\Election;

use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class GetActiveElections
{
    public function execute(?User $user = null, int $perPage = 10): LengthAwarePaginator
    {
        return Election::query()
            ->with(['organization'])
            ->withCount(['positions', 'voters', 'ballots'])
            ->where('status', ElectionStatus::Active)
            ->when($user && ! $user->isAdmin(), function (Builder $query) use ($user) {
                $query->where(function (Builder $subQuery) use ($user) {
                    $subQuery->where('is_public', true)
                        ->orWhereHas('voters', function (Builder $voterQuery) use ($user) {
                            $voterQuery->where('user_id', $user->id);
                        });
                });
            })
            ->orderBy('start_at', 'asc')
            ->paginate($perPage);
    }
}
