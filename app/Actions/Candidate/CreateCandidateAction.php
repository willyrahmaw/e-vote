<?php

namespace App\Actions\Candidate;

use App\Models\Candidate;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Candidate\CandidateService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateCandidateAction
{
    public function __construct(
        private CandidateService $candidateService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, array $data, ?UploadedFile $photo = null): Candidate
    {
        return DB::transaction(function () use ($user, $data, $photo) {
            $photoPath = null;
            if ($photo) {
                $photoPath = $this->candidateService->uploadPhoto($photo);
            }

            $candidate = Candidate::create([
                'organization_id' => $data['organization_id'] ?? null,
                'name' => $data['name'],
                'identifier' => $data['identifier'] ?? null,
                'photo' => $photoPath,
                'bio' => $data['bio'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            $this->auditService->log(
                action: 'CREATE_CANDIDATE',
                user: $user,
                model: $candidate,
                metadata: ['name' => $candidate->name]
            );

            return $candidate;
        });
    }
}
