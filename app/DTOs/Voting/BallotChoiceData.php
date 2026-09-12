<?php

namespace App\DTOs\Voting;

final readonly class BallotChoiceData
{
    public function __construct(
        public ?string $positionId = null,
        public ?string $candidateEntryId = null,
        public ?string $candidateGroupId = null,
        public ?string $optionId = null,
        public bool $isAbstain = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            positionId: $data['position_id'] ?? null,
            candidateEntryId: $data['candidate_entry_id'] ?? null,
            candidateGroupId: $data['candidate_group_id'] ?? null,
            optionId: $data['option_id'] ?? null,
            isAbstain: (bool) ($data['is_abstain'] ?? false),
        );
    }
}
