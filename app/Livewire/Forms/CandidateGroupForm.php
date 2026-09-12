<?php

namespace App\Livewire\Forms;

use App\Models\CandidateGroup;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CandidateGroupForm extends Form
{
    public ?CandidateGroup $group = null;

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public ?string $number = '';

    #[Validate('nullable|string|max:255')]
    public ?string $slogan = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $vision = '';

    #[Validate('nullable|string|max:5000')]
    public ?string $mission = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = '';

    #[Validate('boolean')]
    public bool $is_active = true;

    public function setGroup(CandidateGroup $group): void
    {
        $this->group = $group;
        $this->name = $group->name;
        $this->number = $group->number;
        $this->slogan = $group->slogan;
        $this->vision = $group->vision;
        $this->mission = $group->mission;
        $this->description = $group->description;
        $this->is_active = $group->is_active;
    }
}
