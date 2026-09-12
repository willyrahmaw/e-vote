<?php

namespace App\Services\Voter;

use App\DTOs\Voter\ImportVoterData;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VoterService
{
    /**
     * @param array<int, ImportVoterData> $votersData
     */
    public function importVoters(Election $election, array $votersData): array
    {
        $importedCount = 0;
        $existingCount = 0;
        $errors = [];

        foreach ($votersData as $index => $item) {
            /** @var ImportVoterData $item */
            if (empty($item->email) || ! filter_var($item->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Baris " . ($index + 1) . ": Format email tidak valid ({$item->email})";
                continue;
            }

            // Find or create user
            $user = User::query()->where('email', $item->email)->first();

            if (! $user) {
                $userPassword = $item->password ?: 'password';
                $user = User::create([
                    'organization_id' => $election->organization_id,
                    'name' => $item->name ?: explode('@', $item->email)[0],
                    'email' => $item->email,
                    'identifier' => $item->identifier,
                    'password' => Hash::make($userPassword),
                    'role' => \App\Enums\UserRole::Voter,
                    'is_active' => true,
                ]);
            }

            // Attach to election voter
            $voter = ElectionVoter::query()
                ->where('election_id', $election->id)
                ->where('user_id', $user->id)
                ->first();

            if ($voter) {
                $existingCount++;
            } else {
                ElectionVoter::create([
                    'election_id' => $election->id,
                    'user_id' => $user->id,
                    'is_eligible' => $item->isEligible,
                    'has_voted' => false,
                ]);
                $importedCount++;
            }
        }

        return [
            'imported' => $importedCount,
            'existing' => $existingCount,
            'errors' => $errors,
        ];
    }
}
