<?php

namespace Database\Factories;

use App\Models\CandidateGroup;
use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateGroupFactory extends Factory
{
    protected $model = CandidateGroup::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'name' => 'Pasangan Calon ' . fake()->unique()->numerify('##'),
            'number' => (string) fake()->unique()->numberBetween(1, 99),
            'logo' => null,
            'slogan' => fake()->catchPhrase(),
            'vision' => fake()->paragraph(),
            'mission' => fake()->paragraphs(3, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
