<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionVoterFactory extends Factory
{
    protected $model = ElectionVoter::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'user_id' => User::factory()->voter(),
            'is_eligible' => true,
            'has_voted' => false,
            'voted_at' => null,
        ];
    }

    public function voted(): static
    {
        return $this->state(fn () => [
            'has_voted' => true,
            'voted_at' => now(),
        ]);
    }
}
