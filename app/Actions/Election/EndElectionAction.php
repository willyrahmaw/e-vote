<?php

namespace App\Actions\Election;

use App\Enums\ElectionStatus;
use App\Events\ElectionEnded;
use App\Models\Election;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EndElectionAction
{
    public function execute(User $user, Election $election): Election
    {
        return DB::transaction(function () use ($user, $election) {
            $election->update(['status' => ElectionStatus::Ended]);

            event(new ElectionEnded($election, $user));

            return $election;
        });
    }
}
