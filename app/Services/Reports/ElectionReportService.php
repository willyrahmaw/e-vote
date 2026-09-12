<?php

namespace App\Services\Reports;

use App\Models\Election;
use App\Queries\Voting\GetElectionResults;
use App\Queries\Voting\GetLiveVotingStatistics;

class ElectionReportService
{
    public function __construct(
        private GetElectionResults $resultsQuery,
        private GetLiveVotingStatistics $liveStatsQuery,
    ) {}

    public function generateOfficialReturn(Election $election): array
    {
        $stats = $this->liveStatsQuery->execute($election);
        $results = $this->resultsQuery->execute($election);

        $documentNumber = sprintf(
            'BA-%s/%s/%s/%04d',
            strtoupper(substr($election->slug, 0, 4)),
            now()->format('Y'),
            now()->format('m'),
            crc32($election->id) % 10000
        );

        $integrityChecksum = hash(
            'sha256',
            "{$election->id}:{$stats['total_ballots']}:{$stats['voted_count']}:{$stats['turnout_percentage']}:" . config('app.key')
        );

        return [
            'election' => $election,
            'organization' => $election->organization,
            'document_number' => $documentNumber,
            'documentNumber' => $documentNumber,
            'statistics' => $stats,
            'results' => $results,
            'integrity_checksum' => strtoupper($integrityChecksum),
            'generated_at' => now()->translatedFormat('l, d F Y - H:i:s') . ' ' . \App\Enums\IndonesianTimezone::currentAbbr(),
            'city' => 'Jakarta',
        ];
    }
}
