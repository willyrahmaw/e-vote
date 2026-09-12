<?php

namespace App\Actions\Candidate;

use App\Models\CandidateEntry;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;

class CreateCandidateEntryAction
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, Election $election, ElectionPosition $position, array $data): CandidateEntry
    {
        return DB::transaction(function () use ($user, $election, $position, $data) {
            $entry = CandidateEntry::create([
                'election_id' => $election->id,
                'position_id' => $position->id,
                'candidate_id' => $data['candidate_id'],
                'number' => $data['number'] ?? null,
                'slogan' => $data['slogan'] ?? null,
                'vision' => $data['vision'] ?? null,
                'mission' => $data['mission'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->auditService->log(
                action: 'ADD_CANDIDATE_ENTRY',
                user: $user,
                model: $entry,
                metadata: [
                    'election_id' => $election->id,
                    'position_id' => $position->id,
                    'candidate_id' => $data['candidate_id'],
                ]
            );

            return $entry;
        });
    }
}
