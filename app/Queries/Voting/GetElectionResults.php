<?php

namespace App\Queries\Voting;

use App\Models\BallotChoice;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\ElectionPosition;

class GetElectionResults
{
    public function execute(Election $election): array
    {
        $positions = ElectionPosition::query()
            ->where('election_id', $election->id)
            ->orderBy('sort_order')
            ->get();

        $groups = CandidateGroup::query()
            ->where('election_id', $election->id)
            ->with(['members.candidate', 'members.position'])
            ->orderBy('number')
            ->get();

        $entries = CandidateEntry::query()
            ->where('election_id', $election->id)
            ->with(['candidate', 'position'])
            ->orderBy('number')
            ->get();

        // Count votes per candidate entry
        $entryVoteCounts = BallotChoice::query()
            ->whereHas('ballot', fn ($q) => $q->where('election_id', $election->id))
            ->whereNotNull('candidate_entry_id')
            ->selectRaw('candidate_entry_id, COUNT(*) as total_votes')
            ->groupBy('candidate_entry_id')
            ->pluck('total_votes', 'candidate_entry_id');

        // Count votes per candidate group (paslon)
        $groupVoteCounts = BallotChoice::query()
            ->whereHas('ballot', fn ($q) => $q->where('election_id', $election->id))
            ->whereNotNull('candidate_group_id')
            ->selectRaw('candidate_group_id, COUNT(*) as total_votes')
            ->groupBy('candidate_group_id')
            ->pluck('total_votes', 'candidate_group_id');

        // Count abstain votes per position
        $abstainVoteCounts = BallotChoice::query()
            ->whereHas('ballot', fn ($q) => $q->where('election_id', $election->id))
            ->where('is_abstain', true)
            ->selectRaw('position_id, COUNT(*) as total_votes')
            ->groupBy('position_id')
            ->pluck('total_votes', 'position_id');

        $totalBallots = $election->ballots()->count();

        // Build position results (plain array, cache-safe)
        $positionResults = [];
        foreach ($positions as $position) {
            $posEntries = $entries->where('position_id', $position->id);
            $posTotalVotes = 0;
            $items = [];

            foreach ($posEntries as $entry) {
                $votes = (int) ($entryVoteCounts[$entry->id] ?? 0);
                $posTotalVotes += $votes;
                $items[] = [
                    'id' => $entry->id,
                    'type' => 'entry',
                    'number' => $entry->number,
                    'name' => $entry->candidate?->name ?? 'Kandidat',
                    'slogan' => $entry->slogan,
                    'vision' => $entry->vision,
                    'mission' => $entry->mission,
                    'photo' => $entry->candidate?->photo,
                    'votes' => $votes,
                ];
            }

            $abstainVotes = (int) ($abstainVoteCounts[$position->id] ?? 0);
            $posTotalVotes += $abstainVotes;

            // Calculate percentages and sort items
            foreach ($items as &$item) {
                $item['percentage'] = $posTotalVotes > 0 ? round(($item['votes'] / $posTotalVotes) * 100, 2) : 0;
            }
            unset($item);

            usort($items, fn ($a, $b) => $b['votes'] <=> $a['votes']);

            $positionResults[] = [
                'position_id' => $position->id,
                'position_name' => $position->name,
                'position_description' => $position->description,
                'total_votes' => $posTotalVotes,
                'abstain_votes' => $abstainVotes,
                'abstain_percentage' => $posTotalVotes > 0 ? round(($abstainVotes / $posTotalVotes) * 100, 2) : 0,
                'candidates' => $items,
            ];
        }

        // Build group results (plain array, cache-safe)
        $groupResults = [];
        $groupTotalVotes = 0;
        foreach ($groups as $group) {
            $votes = (int) ($groupVoteCounts[$group->id] ?? 0);
            $groupTotalVotes += $votes;

            $membersList = [];
            foreach ($group->members as $member) {
                $membersList[] = [
                    'id' => $member->id,
                    'name' => $member->candidate?->name ?? 'Calon',
                    'photo' => $member->candidate?->photo,
                    'sort_order' => $member->sort_order,
                ];
            }

            $groupResults[] = [
                'id' => $group->id,
                'name' => $group->name,
                'number' => $group->number,
                'logo' => $group->logo,
                'slogan' => $group->slogan,
                'vision' => $group->vision,
                'mission' => $group->mission,
                'votes' => $votes,
                'members' => $membersList,
            ];
        }

        foreach ($groupResults as &$gResult) {
            $gResult['percentage'] = $groupTotalVotes > 0 ? round(($gResult['votes'] / $groupTotalVotes) * 100, 2) : 0;
        }
        unset($gResult);

        usort($groupResults, fn ($a, $b) => $b['votes'] <=> $a['votes']);

        return [
            'total_ballots' => $totalBallots,
            'position_results' => $positionResults,
            'group_results' => $groupResults,
            'has_groups' => $groups->isNotEmpty(),
        ];
    }
}
