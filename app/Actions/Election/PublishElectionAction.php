<?php

namespace App\Actions\Election;

use App\Enums\ElectionStatus;
use App\Events\ElectionPublished;
use App\Models\Election;
use App\Models\User;
use App\Services\Election\ElectionService;
use Exception;
use Illuminate\Support\Facades\DB;

class PublishElectionAction
{
    public function __construct(
        private ElectionService $electionService,
    ) {}

    public function execute(User $user, Election $election): Election
    {
        $check = $this->electionService->canBePublished($election);
        if (! $check['valid']) {
            throw new Exception(implode(' ', $check['errors']));
        }

        return DB::transaction(function () use ($user, $election) {
            $status = now()->gte($election->start_at) ? ElectionStatus::Active : ElectionStatus::Scheduled;

            $election->update(['status' => $status]);

            event(new ElectionPublished($election, $user));

            return $election;
        });
    }
}
