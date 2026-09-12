<?php

namespace App\Livewire\Public;

use App\Models\Ballot;
use App\Models\Election;
use App\Models\ElectionVoter;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.base')]
#[Title('Verifikasi Surat Suara')]
class BallotVerification extends Component
{
    public string $searchToken = '';
    public ?array $verifiedBallot = null;
    public bool $hasSearched = false;
    public ?string $selectedElectionId = null;

    public function mount(?string $token = null): void
    {
        if ($token) {
            $this->searchToken = trim($token);
            $this->verify();
            return;
        }

        // Auto-verify for logged in voter if they have voted
        if (Auth::check()) {
            $voted = ElectionVoter::query()
                ->where('user_id', Auth::id())
                ->where('has_voted', true)
                ->with('election')
                ->latest('voted_at')
                ->first();

            if ($voted) {
                $this->verifyByVoterRecord($voted);
            }
        }
    }

    public function verifyVoterElection(string $electionVoterId): void
    {
        if (! Auth::check()) {
            return;
        }

        $voterRecord = ElectionVoter::query()
            ->where('id', $electionVoterId)
            ->where('user_id', Auth::id())
            ->where('has_voted', true)
            ->with('election')
            ->first();

        if ($voterRecord) {
            $this->verifyByVoterRecord($voterRecord);
        }
    }

    private function verifyByVoterRecord(ElectionVoter $voterRecord): void
    {
        $election = $voterRecord->election;
        $this->selectedElectionId = $election?->id;
        $this->hasSearched = true;

        $proofHash = strtoupper(hash('sha256', "{$voterRecord->id}:{$voterRecord->election_id}:{$voterRecord->voted_at}:" . config('app.key')));

        $this->verifiedBallot = [
            'id' => $voterRecord->id,
            'masked_id' => 'PEMILIH-' . substr($voterRecord->user_id, 0, 6) . '••••' . substr($voterRecord->user_id, -4),
            'token_hash' => $proofHash,
            'token_short' => substr($proofHash, 0, 16) . '...',
            'election_id' => $election?->id,
            'election_name' => $election?->name ?? 'Pemilihan Terverifikasi',
            'election_status' => $election?->status->label() ?? 'Selesai',
            'submitted_at' => $voterRecord->voted_at?->format('d F Y, H:i:s T') ?? now()->format('d F Y, H:i:s T'),
            'is_valid' => true,
            'is_auto_verified' => true,
            'voter_name' => Auth::user()?->name,
            'voter_identifier' => Auth::user()?->identifier,
            'integrity_seal' => $proofHash,
        ];
    }

    public function verify(): void
    {
        $token = trim($this->searchToken);
        $this->hasSearched = true;

        if (empty($token)) {
            $this->verifiedBallot = null;
            return;
        }

        $ballot = Ballot::query()
            ->where('id', $token)
            ->orWhere('token_hash', $token)
            ->orWhere('token_hash', 'like', $token . '%')
            ->with('election')
            ->first();

        if ($ballot) {
            $this->verifiedBallot = [
                'id' => $ballot->id,
                'masked_id' => substr($ballot->id, 0, 8) . '••••••••' . substr($ballot->id, -4),
                'token_hash' => $ballot->token_hash,
                'token_short' => substr($ballot->token_hash, 0, 16) . '...',
                'election_id' => $ballot->election_id,
                'election_name' => $ballot->election?->name ?? 'Pemilihan Tidak Diketahui',
                'election_status' => $ballot->election?->status->label() ?? 'Selesai',
                'submitted_at' => $ballot->submitted_at?->format('d F Y, H:i:s T') ?? $ballot->created_at->format('d F Y, H:i:s T'),
                'is_valid' => true,
                'is_auto_verified' => false,
                'integrity_seal' => strtoupper(hash('sha256', $ballot->id . $ballot->token_hash)),
            ];
        } else {
            $this->verifiedBallot = null;
        }
    }

    public function resetSearch(): void
    {
        $this->searchToken = '';
        $this->verifiedBallot = null;
        $this->hasSearched = false;
        $this->selectedElectionId = null;
    }

    public function render(): View
    {
        $voterElections = Auth::check()
            ? ElectionVoter::query()
                ->where('user_id', Auth::id())
                ->with('election')
                ->latest('voted_at')
                ->get()
            : collect();

        return view('livewire.public.ballot-verification', [
            'title' => 'Verifikasi Mandiri Surat Suara',
            'voterElections' => $voterElections,
        ]);
    }
}
