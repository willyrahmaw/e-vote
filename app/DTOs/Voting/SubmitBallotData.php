<?php

namespace App\DTOs\Voting;

final readonly class SubmitBallotData
{
    /**
     * @param array<int, BallotChoiceData> $choices
     */
    public function __construct(
        public array $choices,
    ) {}

    public static function fromArray(array $data): self
    {
        $choices = [];
        foreach ($data['choices'] ?? [] as $choice) {
            $choices[] = $choice instanceof BallotChoiceData
                ? $choice
                : BallotChoiceData::fromArray($choice);
        }

        return new self(choices: $choices);
    }
}
