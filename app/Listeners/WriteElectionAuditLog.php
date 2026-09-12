<?php

namespace App\Listeners;

use App\Events\ElectionEnded;
use App\Events\ElectionPublished;
use App\Events\ElectionStarted;
use App\Services\Audit\AuditLogService;

class WriteElectionAuditLog
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    public function handle(ElectionPublished|ElectionStarted|ElectionEnded $event): void
    {
        $action = match (get_class($event)) {
            ElectionPublished::class => 'PUBLISH_ELECTION',
            ElectionStarted::class => 'START_ELECTION',
            ElectionEnded::class => 'END_ELECTION',
        };

        $this->auditService->log(
            action: $action,
            user: $event->user,
            model: $event->election,
            metadata: [
                'election_id' => $event->election->id,
                'name' => $event->election->name,
                'status' => $event->election->status->value,
            ],
        );
    }
}
