<?php

namespace App\Livewire\Forms;

use App\Models\Candidate;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CandidateForm extends Form
{
    public ?Candidate $candidate = null;

    #[Validate('nullable|uuid|exists:organizations,id')]
    public ?string $organization_id = null;

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:100')]
    public ?string $identifier = '';

    #[Validate('nullable|email|max:255')]
    public ?string $email = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $bio = '';

    public function setCandidate(Candidate $candidate): void
    {
        $this->candidate = $candidate;
        $this->organization_id = $candidate->organization_id;
        $this->name = $candidate->name;
        $this->identifier = $candidate->identifier;
        $this->email = $candidate->email;
        $this->bio = $candidate->bio;
    }
}
