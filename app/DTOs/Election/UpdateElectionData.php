<?php

namespace App\DTOs\Election;

use App\Enums\ResultVisibility;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final readonly class UpdateElectionData
{
    public function __construct(
        public ?string $organizationId,
        public string $name,
        public ?string $description,
        public ?string $instructions,
        public CarbonInterface $startAt,
        public CarbonInterface $endAt,
        public ResultVisibility $resultVisibility,
        public bool $isPublic,
        public bool $isLiveResultEnabled,
        public bool $allowAbstain,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: $data['organization_id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            instructions: $data['instructions'] ?? null,
            startAt: Carbon::parse($data['start_at']),
            endAt: Carbon::parse($data['end_at']),
            resultVisibility: isset($data['result_visibility']) ? (is_string($data['result_visibility']) ? ResultVisibility::from($data['result_visibility']) : $data['result_visibility']) : ResultVisibility::AfterElection,
            isPublic: (bool) ($data['is_public'] ?? false),
            isLiveResultEnabled: (bool) ($data['is_live_result_enabled'] ?? true),
            allowAbstain: (bool) ($data['allow_abstain'] ?? false),
        );
    }
}
