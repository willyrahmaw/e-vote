<?php

namespace Tests\Feature;

use App\Actions\Voting\SubmitBallotAction;
use App\DTOs\Voting\SubmitBallotData;
use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Models\Candidate;
use App\Models\CandidateGroup;
use App\Models\CandidateGroupMember;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Queries\Voting\GetElectionResults;
use App\Services\Voting\BallotValidationService;
use App\Services\Voting\ResultService;
use App\Services\Voting\VotingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultVisibilityAndCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_result_calculation_is_correct(): void
    {
        $election = Election::factory()->active()->create();
        $group1 = CandidateGroup::factory()->create(['election_id' => $election->id, 'number' => '01']);
        $group2 = CandidateGroup::factory()->create(['election_id' => $election->id, 'number' => '02']);

        $c1 = Candidate::factory()->create();
        $c2 = Candidate::factory()->create();
        CandidateGroupMember::create(['candidate_group_id' => $group1->id, 'candidate_id' => $c1->id, 'sort_order' => 1]);
        CandidateGroupMember::create(['candidate_group_id' => $group2->id, 'candidate_id' => $c2->id, 'sort_order' => 1]);

        $submitAction = new SubmitBallotAction(new VotingService(), new BallotValidationService());

        // Cast 3 votes for group 1, 1 vote for group 2
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->voter()->create();
            ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $user->id]);
            $submitAction->execute($user, $election, SubmitBallotData::fromArray([
                'choices' => [['candidate_group_id' => $group1->id]],
            ]));
        }

        $user4 = User::factory()->voter()->create();
        ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $user4->id]);
        $submitAction->execute($user4, $election, SubmitBallotData::fromArray([
            'choices' => [['candidate_group_id' => $group2->id]],
        ]));

        $resultsQuery = new GetElectionResults();
        $results = $resultsQuery->execute($election);

        $this->assertEquals(4, $results['total_ballots']);
        $this->assertEquals(3, $results['group_results'][0]['votes']); // Winner: Group 1
        $this->assertEquals(75.0, $results['group_results'][0]['percentage']);
        $this->assertEquals(1, $results['group_results'][1]['votes']);
        $this->assertEquals(25.0, $results['group_results'][1]['percentage']);
    }

    public function test_voter_cannot_access_hidden_results(): void
    {
        $election = Election::factory()->active()->create([
            'result_visibility' => ResultVisibility::Hidden,
        ]);
        $voter = User::factory()->voter()->create();
        ElectionVoter::factory()->voted()->create(['election_id' => $election->id, 'user_id' => $voter->id]);

        $resultService = new ResultService(new GetElectionResults());
        $this->assertFalse($resultService->canUserViewResults($voter, $election));

        $admin = User::factory()->admin()->create();
        $this->assertTrue($resultService->canUserViewResults($admin, $election));
    }

    public function test_after_vote_visibility_only_accessible_after_voting(): void
    {
        $election = Election::factory()->active()->create([
            'result_visibility' => ResultVisibility::AfterVote,
        ]);
        $unvotedVoter = User::factory()->voter()->create();
        ElectionVoter::factory()->create(['election_id' => $election->id, 'user_id' => $unvotedVoter->id, 'has_voted' => false]);

        $resultService = new ResultService(new GetElectionResults());
        $this->assertFalse($resultService->canUserViewResults($unvotedVoter, $election));

        $votedVoter = User::factory()->voter()->create();
        ElectionVoter::factory()->voted()->create(['election_id' => $election->id, 'user_id' => $votedVoter->id]);
        $this->assertTrue($resultService->canUserViewResults($votedVoter, $election));
    }

    public function test_admin_can_view_live_voting_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $election = Election::factory()->active()->create();
        $position = \App\Models\ElectionPosition::factory()->create(['election_id' => $election->id, 'name' => 'Ketua Umum']);
        $cand = Candidate::factory()->create();
        \App\Models\CandidateEntry::create([
            'election_id' => $election->id,
            'position_id' => $position->id,
            'candidate_id' => $cand->id,
            'number' => '01',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.live-voting', ['electionId' => $election->id]));
        $response->assertStatus(200);
        $response->assertSee('Ketua Umum');
    }

    public function test_public_can_view_standalone_live_screen_without_menu(): void
    {
        $election = Election::factory()->active()->create(['name' => 'Pemilihan Presma 2026']);
        $group = CandidateGroup::factory()->create([
            'election_id' => $election->id,
            'name' => 'Paslon 01 Reformasi',
            'number' => '01',
        ]);

        $response = $this->get(route('screen.live', ['election' => $election->id]));
        $response->assertStatus(200);
        $response->assertSee('Pemilihan Presma 2026');
        $response->assertSee('Paslon 01 Reformasi');
        $response->assertSee('LIVE QUICK COUNT');
    }

    public function test_live_screen_dispatches_notification_on_new_vote(): void
    {
        $election = Election::factory()->active()->create();
        $group = CandidateGroup::factory()->create(['election_id' => $election->id]);

        $component = \Livewire\Livewire::test(\App\Livewire\Display\LiveScreen::class, ['election' => $election->id]);

        \App\Models\Ballot::factory()->create([
            'election_id' => $election->id,
            'submitted_at' => now(),
        ]);

        \Illuminate\Support\Facades\Cache::flush();

        $component->call('$refresh')
            ->assertDispatched('new-vote-received');
    }

    public function test_public_can_verify_ballot_using_token_or_uuid(): void
    {
        $election = Election::factory()->active()->create(['name' => 'Pemilu Mahasiswa 2026']);
        $ballot = \App\Models\Ballot::factory()->create([
            'election_id' => $election->id,
            'token_hash' => hash('sha256', 'sample-token-123'),
            'submitted_at' => now(),
        ]);

        // Direct token verification
        $response = $this->get(route('ballot.verify', ['token' => $ballot->id]));
        $response->assertStatus(200);
        $response->assertSee('Terverifikasi &amp; Sah', false);
        $response->assertSee('Pemilu Mahasiswa 2026');

        // Search token component test
        \Livewire\Livewire::test(\App\Livewire\Public\BallotVerification::class)
            ->set('searchToken', substr($ballot->token_hash, 0, 16))
            ->call('verify')
            ->assertSee('Pemilu Mahasiswa 2026')
            ->assertSee('Surat Suara Resmi Tercatat');

        // Invalid token test
        \Livewire\Livewire::test(\App\Livewire\Public\BallotVerification::class)
            ->set('searchToken', 'invalid-token-999999')
            ->call('verify')
            ->assertSee('Surat Suara Tidak Ditemukan');
    }

    public function test_admin_can_view_official_election_report(): void
    {
        $admin = User::factory()->admin()->create();
        $election = Election::factory()->active()->create(['name' => 'Pemilihan Presma 2026']);

        $response = $this->actingAs($admin)->get(route('admin.elections.report', ['election' => $election->id]));
        $response->assertStatus(200);
        $response->assertSee('BERITA ACARA HASIL PENGHITUNGAN SUARA');
        $response->assertSee('Pemilihan Presma 2026');
        $response->assertSee('Ketua Panitia Pemilihan');
    }
}



