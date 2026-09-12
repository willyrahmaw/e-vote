<?php

namespace App\Livewire\Voter;

use App\Actions\Voting\SubmitBallotAction;
use App\DTOs\Voting\BallotChoiceData;
use App\DTOs\Voting\SubmitBallotData;
use App\Exceptions\Voting\VotingException;
use App\Livewire\Forms\VotingForm;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.voter')]
#[Title('Bilik Suara')]
class VotingBallot extends Component
{
    public Election $election;
    public VotingForm $form;

    // Selections array structure
    public array $selectedChoices = [];
    public bool $isReviewModalOpen = false;
    public ?array $previewDetail = null;

    public function mount(Election $election): void
    {
        $this->election = $election->load([
            'positions.candidateEntries.candidate',
            'candidateGroups.members.candidate',
        ]);

        $this->authorize('vote', $this->election);

        // Pre-initialize selection state
        foreach ($this->election->positions as $position) {
            $this->selectedChoices[$position->id] = [
                'entry_id' => null,
                'is_abstain' => false,
            ];
        }

        if ($this->election->candidateGroups()->exists() && $this->election->positions->isEmpty()) {
            $this->selectedChoices['group'] = [
                'group_id' => null,
                'is_abstain' => false,
            ];
        }
    }

    public function selectGroup(string $groupId): void
    {
        $this->selectedChoices['group'] = [
            'group_id' => $groupId,
            'is_abstain' => false,
        ];
    }

    public function abstainGroup(): void
    {
        if (! $this->election->allow_abstain) return;

        $this->selectedChoices['group'] = [
            'group_id' => null,
            'is_abstain' => true,
        ];
    }

    public function selectCandidateEntry(string $positionId, string $entryId): void
    {
        $this->selectedChoices[$positionId] = [
            'entry_id' => $entryId,
            'is_abstain' => false,
        ];
    }

    public function abstainPosition(string $positionId): void
    {
        if (! $this->election->allow_abstain) return;

        $this->selectedChoices[$positionId] = [
            'entry_id' => null,
            'is_abstain' => true,
        ];
    }

    public function reviewBallot(): void
    {
        $this->authorize('vote', $this->election);

        // Validate that each required section has a choice
        if (isset($this->selectedChoices['group'])) {
            $grpChoice = $this->selectedChoices['group'];
            if (! $grpChoice['group_id'] && ! $grpChoice['is_abstain']) {
                $this->dispatch('swal:error', message: 'Harap tentukan pilihan Paslon atau pilih opsi Golput/Abstain.');
                return;
            }
        } else {
            foreach ($this->election->positions as $position) {
                $posChoice = $this->selectedChoices[$position->id] ?? null;
                if ($position->is_required && (! $posChoice || (! $posChoice['entry_id'] && ! $posChoice['is_abstain']))) {
                    $this->dispatch('swal:error', message: "Harap tentukan pilihan untuk posisi '{$position->name}'.");
                    return;
                }
            }
        }

        $this->form->confirmed = false;
        $this->isReviewModalOpen = true;
    }

    public function closeReviewModal(): void
    {
        $this->isReviewModalOpen = false;
    }

    public function openGroupDetail(string $groupId): void
    {
        $group = $this->election->candidateGroups->find($groupId);
        if (! $group) return;

        $this->previewDetail = [
            'type' => 'group',
            'title' => $group->name,
            'number' => $group->number,
            'slogan' => $group->slogan,
            'vision' => $group->vision,
            'mission' => $group->mission,
            'members' => $group->members->map(fn($m) => [
                'name' => $m->candidate->name,
                'role' => $m->sort_order === 1 ? 'Calon Ketua' : 'Calon Wakil',
                'photo' => $m->candidate->photo,
                'bio' => $m->candidate->bio,
                'identifier' => $m->candidate->identifier,
            ])->toArray(),
        ];
    }

    public function openEntryDetail(string $positionId, string $entryId): void
    {
        $position = $this->election->positions->find($positionId);
        $entry = $position?->candidateEntries->find($entryId);
        if (! $entry) return;

        $this->previewDetail = [
            'type' => 'entry',
            'position' => $position->name,
            'title' => $entry->candidate->name,
            'number' => $entry->number,
            'slogan' => $entry->slogan,
            'vision' => $entry->vision,
            'mission' => $entry->mission,
            'members' => [[
                'name' => $entry->candidate->name,
                'role' => $position->name,
                'photo' => $entry->candidate->photo,
                'bio' => $entry->candidate->bio,
                'identifier' => $entry->candidate->identifier,
            ]],
        ];
    }

    public function closeDetailModal(): void
    {
        $this->previewDetail = null;
    }

    public function submit(SubmitBallotAction $action): void
    {
        $this->authorize('vote', $this->election);

        $this->validate([
            'form.confirmed' => 'accepted',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Transform selections into typed DTOs
        $choices = [];
        if (isset($this->selectedChoices['group'])) {
            $grp = $this->selectedChoices['group'];
            $choices[] = new BallotChoiceData(
                candidateGroupId: $grp['group_id'],
                isAbstain: (bool) $grp['is_abstain'],
            );
        } else {
            foreach ($this->selectedChoices as $posId => $choice) {
                if ($posId === 'group') continue;
                $choices[] = new BallotChoiceData(
                    positionId: $posId,
                    candidateEntryId: $choice['entry_id'] ?? null,
                    isAbstain: (bool) ($choice['is_abstain'] ?? false),
                );
            }
        }

        $data = new SubmitBallotData(choices: $choices);

        try {
            $ballot = $action->execute(
                user: $user,
                election: $this->election,
                data: $data,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
            );

            $this->redirectRoute('voter.elections.success', [
                'election' => $this->election->id,
                'ref' => substr($ballot->token_hash, 0, 16),
            ]);
        } catch (VotingException $exception) {
            $this->dispatch('swal:error', message: $exception->getMessage());
        } catch (Exception $exception) {
            $this->dispatch('swal:error', message: 'Gagal mengirimkan suara: ' . $exception->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.voter.voting-ballot', [
            'election' => $this->election,
        ]);
    }
}
