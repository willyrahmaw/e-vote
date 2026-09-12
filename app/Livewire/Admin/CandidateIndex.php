<?php

namespace App\Livewire\Admin;

use App\Actions\Candidate\CreateCandidateAction;
use App\Livewire\Forms\CandidateForm;
use App\Models\Candidate;
use App\Models\Organization;
use App\Models\User;
use App\Services\Candidate\CandidateService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Database Kandidat')]
class CandidateIndex extends Component
{
    use WithPagination, WithFileUploads;

    public CandidateForm $form;
    public string $search = '';
    public bool $isModalOpen = false;

    /** @var TemporaryUploadedFile|null */
    public $photoUpload = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->form->reset();
        $this->photoUpload = null;
        $this->isModalOpen = true;
    }

    public function editCandidate(string $id): void
    {
        $candidate = Candidate::findOrFail($id);
        $this->form->setCandidate($candidate);
        $this->photoUpload = null;
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->form->reset();
        $this->photoUpload = null;
    }

    public function save(CreateCandidateAction $createAction, CandidateService $candidateService): void
    {
        $this->validate([
            'form.name' => 'required|string|min:2|max:255',
            'form.identifier' => 'nullable|string|max:100',
            'form.email' => 'nullable|email|max:255',
            'photoUpload' => 'nullable|image|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($this->form->candidate) {
            // Update
            $candidate = $this->form->candidate;
            $photoPath = $candidate->photo;

            if ($this->photoUpload) {
                $candidateService->deletePhoto($candidate->photo);
                $photoPath = $candidateService->uploadPhoto($this->photoUpload);
            }

            $candidate->update([
                'organization_id' => $this->form->organization_id,
                'name' => $this->form->name,
                'identifier' => $this->form->identifier,
                'email' => $this->form->email,
                'bio' => $this->form->bio,
                'photo' => $photoPath,
            ]);

            $this->dispatch('swal:success', message: 'Data kandidat berhasil diperbarui.');
        } else {
            // Create via Action
            $createAction->execute(
                user: $user,
                data: [
                    'organization_id' => $this->form->organization_id,
                    'name' => $this->form->name,
                    'identifier' => $this->form->identifier,
                    'email' => $this->form->email,
                    'bio' => $this->form->bio,
                ],
                photo: $this->photoUpload
            );

            $this->dispatch('swal:success', message: 'Kandidat baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function deleteCandidate(string $id, CandidateService $candidateService): void
    {
        $candidate = Candidate::findOrFail($id);
        $candidateService->deletePhoto($candidate->photo);
        $candidate->delete();

        $this->dispatch('swal:success', message: 'Kandidat berhasil dihapus.');
    }

    public function render(): View
    {
        $candidates = Candidate::query()
            ->with(['organization'])
            ->withCount(['entries', 'groupMemberships'])
            ->when($this->search, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.candidate-index', [
            'candidates' => $candidates,
            'organizations' => Organization::where('is_active', true)->get(),
        ]);
    }
}
