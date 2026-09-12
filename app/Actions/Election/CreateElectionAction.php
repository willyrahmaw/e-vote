<?php

namespace App\Actions\Election;

use App\DTOs\Election\CreateElectionData;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Election\ElectionService;
use Illuminate\Support\Facades\DB;

class CreateElectionAction
{
    public function __construct(
        private ElectionService $electionService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $creator, CreateElectionData $data): Election
    {
        return DB::transaction(function () use ($creator, $data) {
            $slug = $this->electionService->generateUniqueSlug($data->name);

            $election = Election::create([
                'organization_id' => $data->organizationId,
                'created_by' => $creator->id,
                'name' => $data->name,
                'slug' => $slug,
                'description' => $data->description,
                'instructions' => $data->instructions,
                'start_at' => $data->startAt,
                'end_at' => $data->endAt,
                'status' => $data->status,
                'result_visibility' => $data->resultVisibility,
                'is_public' => $data->isPublic,
                'is_live_result_enabled' => $data->isLiveResultEnabled,
                'allow_abstain' => $data->allowAbstain,
            ]);

            $this->auditService->log(
                action: 'CREATE_ELECTION',
                user: $creator,
                model: $election,
                metadata: ['name' => $election->name, 'slug' => $election->slug]
            );

            return $election;
        });
    }
}
