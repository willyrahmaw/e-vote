<?php

namespace App\Actions\Voter;

use App\DTOs\Voter\ImportVoterData;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Voter\VoterService;
use Illuminate\Support\Facades\DB;

class ImportVotersAction
{
    public function __construct(
        private VoterService $voterService,
        private AuditLogService $auditService,
    ) {}

    /**
     * @param array<int, ImportVoterData> $voters
     */
    public function execute(User $admin, Election $election, array $voters): array
    {
        return DB::transaction(function () use ($admin, $election, $voters) {
            $result = $this->voterService->importVoters($election, $voters);

            $this->auditService->log(
                action: 'IMPORT_VOTERS',
                user: $admin,
                model: $election,
                metadata: [
                    'election_id' => $election->id,
                    'imported_count' => $result['imported'],
                    'existing_count' => $result['existing'],
                    'error_count' => count($result['errors']),
                ]
            );

            return $result;
        });
    }
}
