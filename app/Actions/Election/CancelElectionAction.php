<?php

namespace App\Actions\Election;

use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;

class CancelElectionAction
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, Election $election): Election
    {
        return DB::transaction(function () use ($user, $election) {
            $election->update(['status' => ElectionStatus::Cancelled]);

            $this->auditService->log(
                action: 'CANCEL_ELECTION',
                user: $user,
                model: $election,
                metadata: ['name' => $election->name]
            );

            return $election;
        });
    }
}
