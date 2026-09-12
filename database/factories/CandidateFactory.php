<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->name(),
            'identifier' => 'CAN-' . fake()->unique()->numerify('#####'),
            'photo' => null,
            'bio' => fake()->paragraph(),
            'email' => fake()->safeEmail(),
        ];
    }
}
