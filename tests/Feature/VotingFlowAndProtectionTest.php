<?php

namespace Tests\Feature;

use App\Actions\Voting\SubmitBallotAction;
use App\DTOs\Voting\SubmitBallotData;
use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Exceptions\Voting\AlreadyVotedException;
use App\Exceptions\Voting\ElectionHasEndedException;
use App\Exceptions\Voting\ElectionNotActiveException;
use App\Exceptions\Voting\VoterNotEligibleException;
use App\Models\AuditLog;
use App\Models\Ballot;
use App\Models\Candidate;
use App\Models\CandidateGroup;
use App\Models\CandidateGroupMember;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Services\Voting\BallotValidationService;
use App\Services\Voting\VotingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VotingFlowAndProtectionTest extends TestCase
{
    use RefreshDatabase;

    private SubmitBallotAction $submitBallotAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->submitBallotAction = new SubmitBallotAction(
            new VotingService(),
            new BallotValidationService()
        );
    }

    public function test_non_eligible_voter_cannot_vote(): void
    {
        $election = Election::factory()->active()->create();
        $user = User::factory()->voter()->create();

        // Not registered in ElectionVoter
        $this->expectException(VoterNotEligibleException::class);

        $this->submitBallotAction->execute(
            user: $user,
            election: $election,
            data: SubmitBallotData::fromArray(['choices' => []])
        );
    }

    public function test_voting_before_start_rejected(): void
    {
        $election = Election::factory()->create([
            'status' => ElectionStatus::Active,
            'start_at' => now()->addHour(),
            'end_at' => now()->addDays(2),
        ]);
        $user = User::factory()->voter()->create();
        ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $user->id]);

        $this->expectException(ElectionNotActiveException::class);

        $this->submitBallotAction->execute(
            user: $user,
            election: $election,
            data: SubmitBallotData::fromArray(['choices' => []])
        );
    }

    public function test_voting_after_end_rejected(): void
    {
        $election = Election::factory()->create([
            'status' => ElectionStatus::Active,
            'start_at' => now()->subDays(3),
            'end_at' => now()->subMinute(),
        ]);
        $user = User::factory()->voter()->create();
        ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $user->id]);

        $this->expectException(ElectionHasEndedException::class);

        $this->submitBallotAction->execute(
            user: $user,
            election: $election,
            data: SubmitBallotData::fromArray(['choices' => []])
        );
    }

    public function test_voter_can_vote_once_and_double_voting_is_rejected(): void
    {
        $election = Election::factory()->active()->create(['allow_abstain' => true]);
        $user = User::factory()->voter()->create();
        $voter = ElectionVoter::factory()->create([
            'election_id' => $election->id,
            'user_id' => $user->id,
            'has_voted' => false,
        ]);

        $group = CandidateGroup::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create();
        CandidateGroupMember::create(['candidate_group_id' => $group->id, 'candidate_id' => $candidate->id, 'sort_order' => 1]);

        $data = SubmitBallotData::fromArray([
            'choices' => [
                ['candidate_group_id' => $group->id],
            ],
        ]);

        // 1st submission should succeed
        $ballot = $this->submitBallotAction->execute($user, $election, $data);
        $this->assertInstanceOf(Ballot::class, $ballot);

        // Voter marked as has_voted
        $voter->refresh();
        $this->assertTrue($voter->has_voted);
        $this->assertNotNull($voter->voted_at);

        // 2nd submission must be rejected with AlreadyVotedException
        $this->expectException(AlreadyVotedException::class);
        $this->submitBallotAction->execute($user, $election, $data);
    }

    public function test_ballot_does_not_contain_user_id_for_strict_privacy(): void
    {
        $this->assertFalse(Schema::hasColumn('ballots', 'user_id'));
    }

    public function test_audit_log_records_ballot_submission_without_candidate_selection(): void
    {
        $election = Election::factory()->active()->create(['allow_abstain' => true]);
        $user = User::factory()->voter()->create();
        ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $user->id]);

        $group = CandidateGroup::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create();
        CandidateGroupMember::create(['candidate_group_id' => $group->id, 'candidate_id' => $candidate->id, 'sort_order' => 1]);

        $data = SubmitBallotData::fromArray([
            'choices' => [['candidate_group_id' => $group->id]],
        ]);

        $this->submitBallotAction->execute($user, $election, $data);

        // Check audit log
        $log = AuditLog::where('action', 'SUBMIT_BALLOT')->where('user_id', $user->id)->first();
        $this->assertNotNull($log);
        $this->assertArrayNotHasKey('candidate_id', (array) $log->metadata);
        $this->assertArrayNotHasKey('candidate_group_id', (array) $log->metadata);
    }

    public function test_voter_can_view_voting_ballot_page(): void
    {
        $election = Election::factory()->active()->create();
        $group = CandidateGroup::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create();
        CandidateGroupMember::create(['candidate_group_id' => $group->id, 'candidate_id' => $candidate->id, 'sort_order' => 1]);

        $user = User::factory()->voter()->create();
        ElectionVoter::factory()->create([
            'election_id' => $election->id,
            'user_id' => $user->id,
            'is_eligible' => true,
            'has_voted' => false,
        ]);

        $response = $this->actingAs($user)->get(route('voter.elections.vote', $election));
        $response->assertStatus(200);
        $response->assertSee($election->name);
        $response->assertSee($group->name);
    }
}
