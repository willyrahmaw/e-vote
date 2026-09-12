<?php

namespace App\Actions\Election;

use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Election\ElectionService;
use Illuminate\Support\Facades\DB;

class DuplicateElectionAction
{
    public function __construct(
        private ElectionService $electionService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $user, Election $sourceElection): Election
    {
        return DB::transaction(function () use ($user, $sourceElection) {
            $newName = $sourceElection->name . ' (Salinan)';
            $newSlug = $this->electionService->generateUniqueSlug($newName);

            $newElection = Election::create([
                'organization_id' => $sourceElection->organization_id,
                'created_by' => $user->id,
                'name' => $newName,
                'slug' => $newSlug,
                'description' => $sourceElection->description,
                'instructions' => $sourceElection->instructions,
                'start_at' => now()->addDay(),
                'end_at' => now()->addDays(2),
                'status' => ElectionStatus::Draft,
                'result_visibility' => $sourceElection->result_visibility,
                'is_public' => $sourceElection->is_public,
                'is_live_result_enabled' => $sourceElection->is_live_result_enabled,
                'allow_abstain' => $sourceElection->allow_abstain,
            ]);

            // Duplicate positions
            foreach ($sourceElection->positions as $position) {
                $newPos = $newElection->positions()->create([
                    'name' => $position->name,
                    'description' => $position->description,
                    'min_choices' => $position->min_choices,
                    'max_choices' => $position->max_choices,
                    'is_required' => $position->is_required,
                    'sort_order' => $position->sort_order,
                ]);

                // Duplicate candidate entries for this position
                foreach ($position->candidateEntries as $entry) {
                    $newElection->candidateEntries()->create([
                        'position_id' => $newPos->id,
                        'candidate_id' => $entry->candidate_id,
                        'number' => $entry->number,
                        'slogan' => $entry->slogan,
                        'vision' => $entry->vision,
                        'mission' => $entry->mission,
                        'description' => $entry->description,
                        'is_active' => $entry->is_active,
                    ]);
                }
            }

            // Duplicate candidate groups (if any)
            foreach ($sourceElection->candidateGroups as $group) {
                $newGroup = $newElection->candidateGroups()->create([
                    'name' => $group->name,
                    'number' => $group->number,
                    'logo' => $group->logo,
                    'slogan' => $group->slogan,
                    'vision' => $group->vision,
                    'mission' => $group->mission,
                    'description' => $group->description,
                    'is_active' => $group->is_active,
                ]);

                foreach ($group->members as $member) {
                    $newGroup->members()->create([
                        'candidate_id' => $member->candidate_id,
                        'position_id' => null,
                        'sort_order' => $member->sort_order,
                    ]);
                }
            }

            $this->auditService->log(
                action: 'DUPLICATE_ELECTION',
                user: $user,
                model: $newElection,
                metadata: ['source_id' => $sourceElection->id, 'new_id' => $newElection->id]
            );

            return $newElection;
        });
    }
}
