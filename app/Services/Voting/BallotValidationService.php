<?php

namespace App\Services\Voting;

use App\DTOs\Voting\BallotChoiceData;
use App\DTOs\Voting\SubmitBallotData;
use App\Exceptions\Voting\InvalidBallotException;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\ElectionPosition;

class BallotValidationService
{
    public function validate(Election $election, SubmitBallotData $data): void
    {
        if (empty($data->choices)) {
            throw new InvalidBallotException('Surat suara kosong. Silakan tentukan pilihan Anda.');
        }

        $positions = $election->positions()->get()->keyBy('id');
        $hasCandidateGroups = $election->candidateGroups()->where('is_active', true)->exists();

        // If this election is group/paslon based
        if ($hasCandidateGroups && $positions->isEmpty()) {
            $this->validateGroupOnlyChoices($election, $data);
            return;
        }

        // Validate choice distribution per position
        /** @var array<string, array<int, BallotChoiceData>> $choicesByPosition */
        $choicesByPosition = [];

        foreach ($data->choices as $choice) {
            if ($choice->candidateGroupId) {
                $group = CandidateGroup::query()
                    ->where('id', $choice->candidateGroupId)
                    ->where('election_id', $election->id)
                    ->where('is_active', true)
                    ->first();

                if (! $group) {
                    throw new InvalidBallotException('Paslon/Kelompok kandidat yang dipilih tidak valid atau tidak aktif.');
                }
                continue;
            }

            if (! $choice->positionId || ! $positions->has($choice->positionId)) {
                throw new InvalidBallotException('Posisi jabatan dalam surat suara tidak valid untuk pemilihan ini.');
            }

            $choicesByPosition[$choice->positionId][] = $choice;
        }

        // Validate constraints per position
        foreach ($positions as $positionId => $position) {
            /** @var ElectionPosition $position */
            $posChoices = $choicesByPosition[$positionId] ?? [];
            $choiceCount = count($posChoices);

            if ($position->is_required && $choiceCount === 0) {
                throw new InvalidBallotException("Posisi '{$position->name}' wajib dipilih.");
            }

            if ($choiceCount < $position->min_choices && $position->is_required) {
                throw new InvalidBallotException("Posisi '{$position->name}' membutuhkan minimal {$position->min_choices} pilihan.");
            }

            if ($choiceCount > $position->max_choices) {
                throw new InvalidBallotException("Pilihan untuk '{$position->name}' melebihi batas maksimal ({$position->max_choices} pilihan).");
            }

            foreach ($posChoices as $choice) {
                if ($choice->isAbstain) {
                    if (! $election->allow_abstain) {
                        throw new InvalidBallotException("Pemilihan ini tidak mengizinkan opsi Golput/Abstain.");
                    }
                    continue;
                }

                if ($choice->candidateEntryId) {
                    $entry = CandidateEntry::query()
                        ->where('id', $choice->candidateEntryId)
                        ->where('election_id', $election->id)
                        ->where('position_id', $position->id)
                        ->where('is_active', true)
                        ->first();

                    if (! $entry) {
                        throw new InvalidBallotException("Kandidat yang dipilih untuk posisi '{$position->name}' tidak valid atau tidak aktif.");
                    }
                }
            }
        }
    }

    private function validateGroupOnlyChoices(Election $election, SubmitBallotData $data): void
    {
        if (count($data->choices) !== 1) {
            throw new InvalidBallotException('Hanya diperbolehkan memilih tepat 1 Paslon/Kelompok kandidat.');
        }

        $choice = $data->choices[0];
        if ($choice->isAbstain) {
            if (! $election->allow_abstain) {
                throw new InvalidBallotException('Pemilihan ini tidak mengizinkan opsi Golput/Abstain.');
            }
            return;
        }

        if (! $choice->candidateGroupId) {
            throw new InvalidBallotException('Harap pilih salah satu pasangan calon/kelompok.');
        }

        $group = CandidateGroup::query()
            ->where('id', $choice->candidateGroupId)
            ->where('election_id', $election->id)
            ->where('is_active', true)
            ->first();

        if (! $group) {
            throw new InvalidBallotException('Pasangan calon yang dipilih tidak valid atau tidak aktif.');
        }
    }
}
