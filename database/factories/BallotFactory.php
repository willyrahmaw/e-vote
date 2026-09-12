<?php

namespace Database\Factories;

use App\Models\Ballot;
use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BallotFactory extends Factory
{
    protected $model = Ballot::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'token_hash' => hash('sha256', Str::random(32) . microtime(true)),
            'submitted_at' => now(),
        ];
    }
}
