<?php

namespace App\DTOs\Election;

use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final readonly class CreateElectionData
{
    public function __construct(
        public ?string $organizationId,
        public string $name,
        public ?string $description,
        public ?string $instructions,
        public CarbonInterface $startAt,
        public CarbonInterface $endAt,
        public ElectionStatus $status = ElectionStatus::Draft,
        public ResultVisibility $resultVisibility = ResultVisibility::AfterElection,
        public bool $isPublic = false,
        public bool $isLiveResultEnabled = true,
        public bool $allowAbstain = false,
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
            status: isset($data['status']) ? (is_string($data['status']) ? ElectionStatus::from($data['status']) : $data['status']) : ElectionStatus::Draft,
            resultVisibility: isset($data['result_visibility']) ? (is_string($data['result_visibility']) ? ResultVisibility::from($data['result_visibility']) : $data['result_visibility']) : ResultVisibility::AfterElection,
            isPublic: (bool) ($data['is_public'] ?? false),
            isLiveResultEnabled: (bool) ($data['is_live_result_enabled'] ?? true),
            allowAbstain: (bool) ($data['allow_abstain'] ?? false),
        );
    }
}
