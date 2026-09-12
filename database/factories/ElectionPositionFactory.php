<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionPositionFactory extends Factory
{
    protected $model = ElectionPosition::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'name' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'min_choices' => 1,
            'max_choices' => 1,
            'is_required' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
