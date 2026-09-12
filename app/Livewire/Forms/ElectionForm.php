<?php

namespace App\Livewire\Forms;

use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Models\Election;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ElectionForm extends Form
{
    public ?Election $election = null;

    #[Validate('nullable|uuid|exists:organizations,id')]
    public ?string $organization_id = null;

    #[Validate('required|string|min:3|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $instructions = '';

    #[Validate('required|date')]
    public string $start_at = '';

    #[Validate('required|date|after:start_at')]
    public string $end_at = '';

    #[Validate('required|string')]
    public string $result_visibility = 'after_election';

    #[Validate('boolean')]
    public bool $is_public = true;

    #[Validate('boolean')]
    public bool $is_live_result_enabled = true;

    #[Validate('boolean')]
    public bool $allow_abstain = false;

    public function setElection(Election $election): void
    {
        $this->election = $election;
        $this->organization_id = $election->organization_id;
        $this->name = $election->name;
        $this->description = $election->description;
        $this->instructions = $election->instructions;
        $this->start_at = $election->start_at->format('Y-m-d\TH:i');
        $this->end_at = $election->end_at->format('Y-m-d\TH:i');
        $this->result_visibility = $election->result_visibility->value;
        $this->is_public = $election->is_public;
        $this->is_live_result_enabled = $election->is_live_result_enabled;
        $this->allow_abstain = $election->allow_abstain;
    }
}
