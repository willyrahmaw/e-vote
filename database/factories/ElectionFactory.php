<?php

namespace Database\Factories;

use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Models\Election;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ElectionFactory extends Factory
{
    protected $model = Election::class;

    public function definition(): array
    {
        $name = 'Pemilihan ' . fake()->words(3, true);
        return [
            'organization_id' => Organization::factory(),
            'created_by' => User::factory()->admin(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('####'),
            'description' => fake()->paragraph(),
            'instructions' => 'Gunakan hak pilih Anda dengan bijak dan jujur.',
            'start_at' => now()->subHours(1),
            'end_at' => now()->addDays(2),
            'status' => ElectionStatus::Active,
            'result_visibility' => ResultVisibility::Live,
            'is_public' => true,
            'is_live_result_enabled' => true,
            'allow_abstain' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ElectionStatus::Draft,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => ElectionStatus::Scheduled,
            'start_at' => now()->addDays(1),
            'end_at' => now()->addDays(3),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => ElectionStatus::Active,
            'start_at' => now()->subDay(),
            'end_at' => now()->addDay(),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'status' => ElectionStatus::Ended,
            'start_at' => now()->subDays(3),
            'end_at' => now()->subDay(),
        ]);
    }
}
