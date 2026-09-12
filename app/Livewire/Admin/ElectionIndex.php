<?php

namespace App\Livewire\Admin;

use App\Actions\Election\ActivateElectionAction;
use App\Actions\Election\CancelElectionAction;
use App\Actions\Election\DuplicateElectionAction;
use App\Actions\Election\EndElectionAction;
use App\Actions\Election\PublishElectionAction;
use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen Pemilihan')]
class ElectionIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function publish(string $electionId, PublishElectionAction $action): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('update', $election);
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $election);
            $this->dispatch('swal:success', message: 'Pemilihan berhasil dipublikasikan!');
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function activate(string $electionId, ActivateElectionAction $action): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('update', $election);
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $election);
            $this->dispatch('swal:success', message: 'Sesi pemilihan telah diaktifkan!');
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function end(string $electionId, EndElectionAction $action): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('update', $election);
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $election);
            $this->dispatch('swal:success', message: 'Sesi pemilihan telah ditutup/selesai.');
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function cancel(string $electionId, CancelElectionAction $action): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('update', $election);
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $election);
            $this->dispatch('swal:warning', message: 'Pemilihan telah dibatalkan.');
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function duplicate(string $electionId, DuplicateElectionAction $action): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('create', Election::class);
        /** @var User $user */
        $user = Auth::user();

        try {
            $newElection = $action->execute($user, $election);
            $this->dispatch('swal:success', message: "Salinan pemilihan berhasil dibuat: {$newElection->name}");
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function delete(string $electionId): void
    {
        $election = Election::findOrFail($electionId);
        $this->authorize('delete', $election);
        $election->delete();

        $this->dispatch('swal:success', message: 'Pemilihan berhasil dihapus.');
    }

    public function render(): View
    {
        $elections = Election::query()
            ->with(['organization'])
            ->withCount(['positions', 'candidateGroups', 'voters', 'ballots'])
            ->when($this->search, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($this->statusFilter, function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.election-index', [
            'elections' => $elections,
            'statuses' => ElectionStatus::cases(),
        ]);
    }
}
