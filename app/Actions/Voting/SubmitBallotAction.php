<?php

namespace App\Actions\Voting;

use App\DTOs\Voting\SubmitBallotData;
use App\Events\BallotSubmitted;
use App\Exceptions\Voting\VoterNotEligibleException;
use App\Models\Ballot;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Services\Voting\BallotValidationService;
use App\Services\Voting\VotingService;
use Illuminate\Support\Facades\DB;

final class SubmitBallotAction
{
    public function __construct(
        private VotingService $votingService,
        private BallotValidationService $ballotValidator,
    ) {}

    public function execute(
        User $user,
        Election $election,
        SubmitBallotData $data,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): Ballot {
        $ballot = DB::transaction(function () use ($user, $election, $data) {
            // Row-level lock on election_voters to prevent race conditions / double voting
            $voter = ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $voter) {
                throw new VoterNotEligibleException();
            }

            // Validate voter status and account
            $this->votingService->ensureEligible($voter, $election, $user);

            // Validate election state & voting period
            $this->votingService->validateElectionState($election);

            // Validate choices integrity
            $this->ballotValidator->validate($election, $data);

            // Create anonymous ballot (strict privacy - zero user_id)
            $ballot = $this->votingService->createAnonymousBallot($election);

            // Store choices for the ballot
            $this->votingService->storeChoices($ballot, $data);

            // Mark voter as having voted
            $voter->update([
                'has_voted' => true,
                'voted_at' => now(),
            ]);

            return $ballot;
        });

        // Dispatch domain event outside transaction boundary for audit logging
        event(new BallotSubmitted(
            user: $user,
            election: $election,
            tokenHash: $ballot->token_hash,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        ));

        return $ballot;
    }
}
