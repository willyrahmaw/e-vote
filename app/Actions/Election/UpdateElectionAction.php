<?php

namespace App\Actions\Election;

use App\DTOs\Election\UpdateElectionData;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Election\ElectionService;
use Illuminate\Support\Facades\DB;

class UpdateElectionAction
{
    public function __construct(
        private ElectionService $electionService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, Election $election, UpdateElectionData $data): Election
    {
        return DB::transaction(function () use ($user, $election, $data) {
            $slug = $election->name !== $data->name
                ? $this->electionService->generateUniqueSlug($data->name, $election->id)
                : $election->slug;

            $election->update([
                'organization_id' => $data->organizationId,
                'name' => $data->name,
                'slug' => $slug,
                'description' => $data->description,
                'instructions' => $data->instructions,
                'start_at' => $data->startAt,
                'end_at' => $data->endAt,
                'result_visibility' => $data->resultVisibility,
                'is_public' => $data->isPublic,
                'is_live_result_enabled' => $data->isLiveResultEnabled,
                'allow_abstain' => $data->allowAbstain,
            ]);

            $this->auditService->log(
                action: 'UPDATE_ELECTION',
                user: $user,
                model: $election,
                metadata: ['name' => $election->name]
            );

            return $election;
        });
    }
}
