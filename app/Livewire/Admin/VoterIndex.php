<?php

namespace App\Livewire\Admin;

use App\Actions\Voter\AssignVoterAction;
use App\Actions\Voter\ImportVotersAction;
use App\DTOs\Voter\ImportVoterData;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Queries\Voter\GetElectionVoters;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Daftar Pemilih Tetap (DPT)')]
class VoterIndex extends Component
{
    use WithPagination, WithFileUploads;

    public ?string $selectedElectionId = null;
    public string $search = '';
    public string $filterStatus = '';

    /** @var TemporaryUploadedFile|null */
    public $csvFile = null;

    public array $importPreview = [];
    public bool $isImportModalOpen = false;

    // Manual Assign State
    public string $newVoterName = '';
    public string $newVoterEmail = '';
    public string $newVoterIdentifier = '';
    public string $newVoterPassword = '';
    public bool $isAssignModalOpen = false;

    public function mount(?string $electionId = null): void
    {
        $this->selectedElectionId = $electionId ?? Election::latest()->value('id');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedElectionId(): void
    {
        $this->resetPage();
    }

    public function updatedCsvFile(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        if (! $this->csvFile) return;

        $path = $this->csvFile->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        $this->importPreview = [];
        if (count($rows) > 0) {
            // Check if first row is header
            $header = array_map('strtolower', array_map('trim', $rows[0]));
            $hasHeader = in_array('email', $header) || in_array('name', $header);

            $startIndex = $hasHeader ? 1 : 0;
            for ($i = $startIndex; $i < min(count($rows), $startIndex + 50); $i++) {
                $row = $rows[$i];
                if (empty($row[0])) continue;

                $this->importPreview[] = [
                    'name' => $row[0] ?? '',
                    'email' => $row[1] ?? '',
                    'identifier' => $row[2] ?? '',
                    'password' => ! empty($row[3]) ? $row[3] : 'password (default)',
                ];
            }
        }
    }

    public function downloadTemplate()
    {
        $csvHeader = "Nama Lengkap,Alamat Email,NIM / NIK / ID,Password (Opsional)\n";
        $csvSample1 = "Budi Santoso,budi@example.com,2026001,password\n";
        $csvSample2 = "Siti Rahma,siti@example.com,2026002,\n";

        return response()->streamDownload(function () use ($csvHeader, $csvSample1, $csvSample2) {
            echo $csvHeader . $csvSample1 . $csvSample2;
        }, 'template-import-dpt.csv', ['Content-Type' => 'text/csv']);
    }

    public function processImport(ImportVotersAction $action): void
    {
        $this->validate([
            'selectedElectionId' => 'required|uuid|exists:elections,id',
            'csvFile' => 'required|file',
        ]);

        $election = Election::findOrFail($this->selectedElectionId);
        $path = $this->csvFile->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        $votersData = [];
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        $hasHeader = in_array('email', $header) || in_array('name', $header);
        $startIndex = $hasHeader ? 1 : 0;

        for ($i = $startIndex; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[0])) continue;

            $votersData[] = ImportVoterData::fromArray([
                'name' => $row[0] ?? '',
                'email' => $row[1] ?? '',
                'identifier' => $row[2] ?? null,
                'password' => ! empty($row[3]) ? trim($row[3]) : null,
            ]);
        }

        /** @var User $admin */
        $admin = Auth::user();

        try {
            $result = $action->execute($admin, $election, $votersData);
            $msg = "Import selesai! Berhasil: {$result['imported']}, Sudah Ada: {$result['existing']}. Password default: 'password' (jika tidak ditentukan di file CSV).";
            if (! empty($result['errors'])) {
                $msg .= ' Beberapa baris dilewati karena format tidak valid.';
            }

            $this->dispatch('swal:success', message: $msg);
            $this->isImportModalOpen = false;
            $this->csvFile = null;
            $this->importPreview = [];
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function assignVoter(AssignVoterAction $action): void
    {
        $this->validate([
            'selectedElectionId' => 'required|uuid|exists:elections,id',
            'newVoterName' => 'required|string|max:255',
            'newVoterEmail' => 'required|email|max:255',
            'newVoterIdentifier' => 'nullable|string|max:100',
            'newVoterPassword' => 'nullable|string|min:6',
        ]);

        $election = Election::findOrFail($this->selectedElectionId);
        $voterPassword = ! empty($this->newVoterPassword) ? $this->newVoterPassword : 'password';

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $this->newVoterEmail],
            [
                'name' => $this->newVoterName,
                'identifier' => $this->newVoterIdentifier,
                'password' => bcrypt($voterPassword),
                'role' => \App\Enums\UserRole::Voter,
                'is_active' => true,
            ]
        );

        /** @var User $admin */
        $admin = Auth::user();

        try {
            $action->execute($admin, $election, $user);
            $this->dispatch('swal:success', message: "Pemilih berhasil didaftarkan. Password login: '{$voterPassword}'.");
            $this->isAssignModalOpen = false;
            $this->newVoterName = '';
            $this->newVoterEmail = '';
            $this->newVoterIdentifier = '';
            $this->newVoterPassword = '';
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function toggleEligibility(string $voterId): void
    {
        $voter = ElectionVoter::findOrFail($voterId);
        $voter->update(['is_eligible' => ! $voter->is_eligible]);
        $this->dispatch('swal:success', message: 'Status hak suara pemilih berhasil diperbarui.');
    }

    public function deleteVoter(string $voterId): void
    {
        $voter = ElectionVoter::findOrFail($voterId);
        $voter->delete();
        $this->dispatch('swal:success', message: 'Pemilih berhasil dihapus dari pemilihan ini.');
    }

    public function render(GetElectionVoters $query): View
    {
        $elections = Election::orderBy('name')->get();
        $currentElection = $this->selectedElectionId ? Election::find($this->selectedElectionId) : null;

        $voters = $currentElection
            ? $query->execute($currentElection, $this->search, $this->filterStatus, 15)
            : null;

        return view('livewire.admin.voter-index', [
            'elections' => $elections,
            'currentElection' => $currentElection,
            'voters' => $voters,
        ]);
    }
}
