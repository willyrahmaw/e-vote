<?php

namespace App\Services\Voting;

use App\DTOs\Voting\SubmitBallotData;
use App\Exceptions\Voting\AlreadyVotedException;
use App\Exceptions\Voting\ElectionHasEndedException;
use App\Exceptions\Voting\ElectionNotActiveException;
use App\Exceptions\Voting\VoterNotEligibleException;
use App\Models\Ballot;
use App\Models\BallotChoice;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use Illuminate\Support\Str;

class VotingService
{
    public function ensureEligible(?ElectionVoter $voter, Election $election, ?User $user = null): void
    {
        if ($user && ! $user->is_active) {
            throw new VoterNotEligibleException('Akun pengguna Anda saat ini dinonaktifkan.');
        }

        if (! $voter || ! $voter->is_eligible) {
            throw new VoterNotEligibleException('Anda tidak terdaftar sebagai pemilih sah dalam pemilihan ini.');
        }

        if ($voter->has_voted) {
            throw new AlreadyVotedException();
        }
    }

    public function validateElectionState(Election $election): void
    {
        if (! $election->isActive()) {
            throw new ElectionNotActiveException();
        }

        if (now()->lt($election->start_at)) {
            throw new ElectionNotActiveException('Waktu pemilihan belum dimulai.');
        }

        if (now()->gt($election->end_at)) {
            throw new ElectionHasEndedException();
        }
    }

    public function createAnonymousBallot(Election $election): Ballot
    {
        $tokenHash = hash('sha256', Str::random(40) . microtime(true) . $election->id);

        return Ballot::create([
            'election_id' => $election->id,
            'token_hash' => $tokenHash,
            'submitted_at' => now(),
        ]);
    }

    public function storeChoices(Ballot $ballot, SubmitBallotData $data): void
    {
        foreach ($data->choices as $choice) {
            BallotChoice::create([
                'ballot_id' => $ballot->id,
                'position_id' => $choice->positionId,
                'candidate_entry_id' => $choice->candidateEntryId,
                'candidate_group_id' => $choice->candidateGroupId,
                'option_id' => $choice->optionId,
                'is_abstain' => $choice->isAbstain,
            ]);
        }
    }
}
