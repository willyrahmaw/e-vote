<?php

namespace App\Actions\Voter;

use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Exception;
use Illuminate\Support\Facades\DB;

class AssignVoterAction
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    public function execute(User $admin, Election $election, User $voterUser, bool $isEligible = true): ElectionVoter
    {
        return DB::transaction(function () use ($admin, $election, $voterUser, $isEligible) {
            $existing = ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('user_id', $voterUser->id)
                ->first();

            if ($existing) {
                throw new Exception('Pemilih ini sudah terdaftar di pemilihan ini.');
            }

            $voter = ElectionVoter::create([
                'election_id' => $election->id,
                'user_id' => $voterUser->id,
                'is_eligible' => $isEligible,
                'has_voted' => false,
            ]);

            $this->auditService->log(
                action: 'ASSIGN_VOTER',
                user: $admin,
                model: $voter,
                metadata: [
                    'election_id' => $election->id,
                    'voter_id' => $voterUser->id,
                    'voter_email' => $voterUser->email,
                ]
            );

            return $voter;
        });
    }
}
