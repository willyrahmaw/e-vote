<?php

namespace App\Livewire\Admin;

use App\Actions\Election\CreateElectionAction;
use App\Actions\Election\PublishElectionAction;
use App\Actions\Election\UpdateElectionAction;
use App\DTOs\Election\CreateElectionData;
use App\DTOs\Election\UpdateElectionData;
use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Livewire\Forms\ElectionForm;
use App\Models\Candidate;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\Organization;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Wizard Pemilihan Multi-Step')]
class ElectionWizard extends Component
{
    public ElectionForm $form;
    public ?Election $election = null;

    public int $currentStep = 1;
    public int $totalSteps = 9;

    // Step 2 & 3: Model & Positions
    public string $electionModel = 'group'; // 'group' (paslon) or 'position' (individual/multi-position)
    public array $newPosition = [
        'name' => '',
        'min_choices' => 1,
        'max_choices' => 1,
        'is_required' => true,
    ];

    // Step 4 & 5: Paslon Group or Candidate Entry Setup
    public array $newGroup = [
        'name' => '',
        'number' => '',
        'slogan' => '',
        'vision' => '',
        'mission' => '',
        'leader_id' => '',
        'vice_id' => '',
    ];

    public array $newEntry = [
        'position_id' => '',
        'candidate_id' => '',
        'number' => '',
        'slogan' => '',
        'vision' => '',
        'mission' => '',
    ];

    // Step 5: Vision & Mission live editing
    public array $editingVision = [];
    public array $editingMission = [];
    public array $editingSlogan = [];

    // Step 6: Quick Voter Add
    public string $selectedVoterUserId = '';

    public function mount(?string $id = null): void
    {
        $this->authorize('create', Election::class);

        if ($id) {
            $this->election = Election::with(['positions', 'candidateGroups.members.candidate', 'candidateEntries.candidate', 'voters.user'])->findOrFail($id);
            $this->form->setElection($this->election);
            $this->electionModel = $this->election->candidateGroups()->exists() ? 'group' : 'position';
        } else {
            $this->form->start_at = now()->addDay()->format('Y-m-d\T09:00');
            $this->form->end_at = now()->addDays(2)->format('Y-m-d\T17:00');
            $this->form->result_visibility = ResultVisibility::Live->value;
            $this->form->is_public = true;
            $this->form->is_live_result_enabled = true;
            $this->form->allow_abstain = true;
        }
    }

    public function nextStep(CreateElectionAction $createAction, UpdateElectionAction $updateAction): void
    {
        if ($this->currentStep === 1) {
            $this->validateStep1();

            /** @var User $user */
            $user = Auth::user();

            // Save or update base election record
            $data = CreateElectionData::fromArray([
                'organization_id' => $this->form->organization_id,
                'name' => $this->form->name,
                'description' => $this->form->description,
                'instructions' => $this->form->instructions,
                'start_at' => $this->form->start_at,
                'end_at' => $this->form->end_at,
                'result_visibility' => $this->form->result_visibility,
                'is_public' => $this->form->is_public,
                'is_live_result_enabled' => $this->form->is_live_result_enabled,
                'allow_abstain' => $this->form->allow_abstain,
            ]);

            if (! $this->election) {
                $this->election = $createAction->execute($user, $data);
                $this->form->setElection($this->election);
            } else {
                $updateData = UpdateElectionData::fromArray([
                    'organization_id' => $this->form->organization_id,
                    'name' => $this->form->name,
                    'description' => $this->form->description,
                    'instructions' => $this->form->instructions,
                    'start_at' => $this->form->start_at,
                    'end_at' => $this->form->end_at,
                    'result_visibility' => $this->form->result_visibility,
                    'is_public' => $this->form->is_public,
                    'is_live_result_enabled' => $this->form->is_live_result_enabled,
                    'allow_abstain' => $this->form->allow_abstain,
                ]);
                $this->election = $updateAction->execute($user, $this->election, $updateData);
            }
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            if ($this->currentStep === 5) {
                $this->initVisionMission();
            }
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            if ($this->currentStep === 5) {
                $this->initVisionMission();
            }
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            if ($this->currentStep === 5) {
                $this->initVisionMission();
            }
        }
    }

    public function initVisionMission(): void
    {
        if (! $this->election) return;

        if ($this->electionModel === 'group') {
            $this->election->load('candidateGroups');
            foreach ($this->election->candidateGroups as $grp) {
                $this->editingVision[$grp->id] = $grp->vision ?? '';
                $this->editingMission[$grp->id] = $grp->mission ?? '';
                $this->editingSlogan[$grp->id] = $grp->slogan ?? '';
            }
        } else {
            $this->election->load('candidateEntries.candidate', 'candidateEntries.position');
            foreach ($this->election->candidateEntries as $ent) {
                $this->editingVision[$ent->id] = $ent->vision ?? '';
                $this->editingMission[$ent->id] = $ent->mission ?? '';
                $this->editingSlogan[$ent->id] = $ent->slogan ?? '';
            }
        }
    }

    public function saveVisionMission(string $id): void
    {
        if (! $this->election) return;

        if ($this->electionModel === 'group') {
            CandidateGroup::where('id', $id)->where('election_id', $this->election->id)->update([
                'vision' => $this->editingVision[$id] ?? null,
                'mission' => $this->editingMission[$id] ?? null,
                'slogan' => $this->editingSlogan[$id] ?? null,
            ]);
            $this->election->load('candidateGroups.members.candidate');
        } else {
            CandidateEntry::where('id', $id)->where('election_id', $this->election->id)->update([
                'vision' => $this->editingVision[$id] ?? null,
                'mission' => $this->editingMission[$id] ?? null,
                'slogan' => $this->editingSlogan[$id] ?? null,
            ]);
            $this->election->load('candidateEntries.candidate', 'candidateEntries.position');
        }

        $this->dispatch('swal:success', message: 'Visi & Misi berhasil disimpan.');
    }

    private function validateStep1(): void
    {
        $this->validate([
            'form.name' => 'required|string|min:3|max:255',
            'form.start_at' => 'required|date',
            'form.end_at' => 'required|date|after:form.start_at',
        ]);
    }

    // Positions handler
    public function addPosition(): void
    {
        $this->validate([
            'newPosition.name' => 'required|string|max:100',
            'newPosition.min_choices' => 'required|integer|min:1',
            'newPosition.max_choices' => 'required|integer|min:1|gte:newPosition.min_choices',
        ]);

        if (! $this->election) return;

        $this->election->positions()->create([
            'name' => $this->newPosition['name'],
            'min_choices' => $this->newPosition['min_choices'],
            'max_choices' => $this->newPosition['max_choices'],
            'is_required' => $this->newPosition['is_required'],
            'sort_order' => $this->election->positions()->count() + 1,
        ]);

        $this->newPosition = ['name' => '', 'min_choices' => 1, 'max_choices' => 1, 'is_required' => true];
        $this->election->load('positions');
        $this->dispatch('swal:success', message: 'Posisi jabatan berhasil ditambahkan.');
    }

    public function deletePosition(string $positionId): void
    {
        ElectionPosition::where('id', $positionId)->where('election_id', $this->election->id)->delete();
        $this->election->load('positions');
        $this->dispatch('swal:success', message: 'Posisi berhasil dihapus.');
    }

    // Paslon Group handler
    public function addCandidateGroup(): void
    {
        $this->validate([
            'newGroup.name' => 'required|string|max:100',
            'newGroup.number' => 'nullable|string|max:20',
            'newGroup.leader_id' => 'required|uuid|exists:candidates,id',
            'newGroup.vice_id' => 'nullable|uuid|exists:candidates,id|different:newGroup.leader_id',
        ]);

        if (! $this->election) return;

        $group = $this->election->candidateGroups()->create([
            'name' => $this->newGroup['name'],
            'number' => $this->newGroup['number'],
            'slogan' => $this->newGroup['slogan'],
            'vision' => $this->newGroup['vision'],
            'mission' => $this->newGroup['mission'],
            'is_active' => true,
        ]);

        $group->members()->create(['candidate_id' => $this->newGroup['leader_id'], 'sort_order' => 1]);
        if (! empty($this->newGroup['vice_id'])) {
            $group->members()->create(['candidate_id' => $this->newGroup['vice_id'], 'sort_order' => 2]);
        }

        $this->newGroup = ['name' => '', 'number' => '', 'slogan' => '', 'vision' => '', 'mission' => '', 'leader_id' => '', 'vice_id' => ''];
        $this->election->load('candidateGroups.members.candidate');
        $this->dispatch('swal:success', message: 'Paslon berhasil didaftarkan.');
    }

    public function deleteCandidateGroup(string $groupId): void
    {
        CandidateGroup::where('id', $groupId)->where('election_id', $this->election->id)->delete();
        $this->election->load('candidateGroups.members.candidate');
        $this->dispatch('swal:success', message: 'Paslon berhasil dihapus.');
    }

    // Candidate Entry (Individual) Handler
    public function addCandidateEntry(): void
    {
        $this->validate([
            'newEntry.position_id' => 'required|uuid|exists:election_positions,id',
            'newEntry.candidate_id' => 'required|uuid|exists:candidates,id',
            'newEntry.number' => 'nullable|string|max:20',
        ]);

        if (! $this->election) return;

        $this->election->candidateEntries()->create([
            'position_id' => $this->newEntry['position_id'],
            'candidate_id' => $this->newEntry['candidate_id'],
            'number' => $this->newEntry['number'],
            'slogan' => $this->newEntry['slogan'],
            'vision' => $this->newEntry['vision'],
            'mission' => $this->newEntry['mission'],
            'is_active' => true,
        ]);

        $this->newEntry = ['position_id' => '', 'candidate_id' => '', 'number' => '', 'slogan' => '', 'vision' => '', 'mission' => ''];
        $this->election->load('candidateEntries.candidate', 'candidateEntries.position');
        $this->dispatch('swal:success', message: 'Kandidat berhasil ditambahkan ke posisi.');
    }

    public function deleteCandidateEntry(string $entryId): void
    {
        $this->election->candidateEntries()->where('id', $entryId)->delete();
        $this->election->load('candidateEntries.candidate', 'candidateEntries.position');
        $this->dispatch('swal:success', message: 'Kandidat berhasil dihapus.');
    }

    // Voter assignment
    public function addVoter(): void
    {
        $this->validate([
            'selectedVoterUserId' => 'required|uuid|exists:users,id',
        ]);

        if (! $this->election) return;

        $exists = $this->election->voters()->where('user_id', $this->selectedVoterUserId)->exists();
        if ($exists) {
            $this->dispatch('swal:error', message: 'Pemilih ini sudah ada dalam daftar.');
            return;
        }

        $this->election->voters()->create([
            'user_id' => $this->selectedVoterUserId,
            'is_eligible' => true,
            'has_voted' => false,
        ]);

        $this->selectedVoterUserId = '';
        $this->election->load('voters.user');
        $this->dispatch('swal:success', message: 'Pemilih berhasil ditambahkan.');
    }

    public function addAllVoters(): void
    {
        if (! $this->election) return;

        $voterUsers = User::where('role', \App\Enums\UserRole::Voter)->get();
        $added = 0;

        foreach ($voterUsers as $vUser) {
            $exists = $this->election->voters()->where('user_id', $vUser->id)->exists();
            if (! $exists) {
                $this->election->voters()->create([
                    'user_id' => $vUser->id,
                    'is_eligible' => true,
                    'has_voted' => false,
                ]);
                $added++;
            }
        }

        $this->election->load('voters.user');
        $this->dispatch('swal:success', message: "Berhasil menambahkan {$added} pemilih ke pemilihan.");
    }

    public function deleteVoter(string $voterId): void
    {
        $this->election->voters()->where('id', $voterId)->delete();
        $this->election->load('voters.user');
        $this->dispatch('swal:success', message: 'Pemilih berhasil dihapus dari daftar.');
    }

    // Publish action
    public function publish(PublishElectionAction $action): void
    {
        if (! $this->election) return;
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $this->election);
            $this->dispatch('swal:success', message: 'Pemilihan berhasil dipublikasikan dan siap digunakan!');
            $this->redirectRoute('admin.elections.index');
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.admin.election-wizard', [
            'organizations' => Organization::where('is_active', true)->get(),
            'candidates' => Candidate::orderBy('name')->get(),
            'availableUsers' => User::where('role', \App\Enums\UserRole::Voter)->orderBy('name')->get(),
            'visibilities' => ResultVisibility::cases(),
        ]);
    }
}
