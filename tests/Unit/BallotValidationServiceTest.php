<?php

namespace Tests\Unit;

use App\DTOs\Voting\BallotChoiceData;
use App\DTOs\Voting\SubmitBallotData;
use App\Exceptions\Voting\InvalidBallotException;
use App\Models\Candidate;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Services\Voting\BallotValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BallotValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private BallotValidationService $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new BallotValidationService();
    }

    public function test_empty_choices_throws_exception(): void
    {
        $election = Election::factory()->create();
        $data = new SubmitBallotData(choices: []);

        $this->expectException(InvalidBallotException::class);
        $this->validator->validate($election, $data);
    }

    public function test_position_must_belong_to_election(): void
    {
        $election1 = Election::factory()->create();
        $election2 = Election::factory()->create();

        $posElection2 = ElectionPosition::factory()->create(['election_id' => $election2->id]);
        $cand = Candidate::factory()->create();
        $entry = CandidateEntry::create([
            'election_id' => $election2->id,
            'position_id' => $posElection2->id,
            'candidate_id' => $cand->id,
        ]);

        $posElection1 = ElectionPosition::factory()->create(['election_id' => $election1->id]);

        $data = new SubmitBallotData(choices: [
            new BallotChoiceData(positionId: $posElection2->id, candidateEntryId: $entry->id),
        ]);

        $this->expectException(InvalidBallotException::class);
        $this->validator->validate($election1, $data);
    }

    public function test_candidate_group_must_belong_to_election(): void
    {
        $election1 = Election::factory()->create();
        $election2 = Election::factory()->create();

        $groupElection2 = CandidateGroup::factory()->create(['election_id' => $election2->id]);

        $data = new SubmitBallotData(choices: [
            new BallotChoiceData(candidateGroupId: $groupElection2->id),
        ]);

        $this->expectException(InvalidBallotException::class);
        $this->validator->validate($election1, $data);
    }

    public function test_max_choices_enforced(): void
    {
        $election = Election::factory()->create();
        $position = ElectionPosition::factory()->create([
            'election_id' => $election->id,
            'min_choices' => 1,
            'max_choices' => 1,
        ]);

        $c1 = Candidate::factory()->create();
        $c2 = Candidate::factory()->create();

        $e1 = CandidateEntry::create(['election_id' => $election->id, 'position_id' => $position->id, 'candidate_id' => $c1->id]);
        $e2 = CandidateEntry::create(['election_id' => $election->id, 'position_id' => $position->id, 'candidate_id' => $c2->id]);

        $data = new SubmitBallotData(choices: [
            new BallotChoiceData(positionId: $position->id, candidateEntryId: $e1->id),
            new BallotChoiceData(positionId: $position->id, candidateEntryId: $e2->id),
        ]);

        $this->expectException(InvalidBallotException::class);
        $this->validator->validate($election, $data);
    }

    public function test_abstain_rejected_when_not_allowed(): void
    {
        $election = Election::factory()->create(['allow_abstain' => false]);
        $group = CandidateGroup::factory()->create(['election_id' => $election->id]);

        $data = new SubmitBallotData(choices: [
            new BallotChoiceData(isAbstain: true),
        ]);

        $this->expectException(InvalidBallotException::class);
        $this->validator->validate($election, $data);
    }
}
