<?php

namespace App\Actions\Election;

use App\Enums\ElectionStatus;
use App\Events\ElectionStarted;
use App\Models\Election;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivateElectionAction
{
    public function execute(User $user, Election $election): Election
    {
        return DB::transaction(function () use ($user, $election) {
            $election->update(['status' => ElectionStatus::Active]);

            event(new ElectionStarted($election, $user));

            return $election;
        });
    }
}
