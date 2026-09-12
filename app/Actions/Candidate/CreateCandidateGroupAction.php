<?php

namespace App\Actions\Candidate;

use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Candidate\CandidateService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateCandidateGroupAction
{
    public function __construct(
        private CandidateService $candidateService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, Election $election, array $data, array $members = [], ?UploadedFile $logo = null): CandidateGroup
    {
        return DB::transaction(function () use ($user, $election, $data, $members, $logo) {
            $logoPath = null;
            if ($logo) {
                $logoPath = $this->candidateService->uploadPhoto($logo, 'candidate-groups');
            }

            $group = CandidateGroup::create([
                'election_id' => $election->id,
                'name' => $data['name'],
                'number' => $data['number'] ?? null,
                'logo' => $logoPath,
                'slogan' => $data['slogan'] ?? null,
                'vision' => $data['vision'] ?? null,
                'mission' => $data['mission'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            foreach ($members as $sortOrder => $memberData) {
                $group->members()->create([
                    'candidate_id' => $memberData['candidate_id'],
                    'position_id' => $memberData['position_id'] ?? null,
                    'sort_order' => $sortOrder + 1,
                ]);
            }

            $this->auditService->log(
                action: 'CREATE_CANDIDATE_GROUP',
                user: $user,
                model: $group,
                metadata: ['name' => $group->name, 'election_id' => $election->id]
            );

            return $group;
        });
    }
}
