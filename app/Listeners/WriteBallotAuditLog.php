<?php

namespace App\Listeners;

use App\Events\BallotSubmitted;
use App\Services\Audit\AuditLogService;

class WriteBallotAuditLog
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    public function handle(BallotSubmitted $event): void
    {
        $this->auditService->log(
            action: 'SUBMIT_BALLOT',
            user: $event->user,
            model: $event->election,
            metadata: [
                'election_id' => $event->election->id,
                'election_name' => $event->election->name,
                'token_hash' => $event->tokenHash,
                'description' => "Pengguna {$event->user->name} ({$event->user->email}) telah menggunakan hak suaranya pada pemilihan: {$event->election->name}",
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }
}
