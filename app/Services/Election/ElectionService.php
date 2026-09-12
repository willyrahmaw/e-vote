<?php

namespace App\Services\Election;

use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use Illuminate\Support\Str;

class ElectionService
{
    public function generateUniqueSlug(string $name, ?string $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Election::query()
            ->where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function canBePublished(Election $election): array
    {
        $errors = [];

        if ($election->positions()->count() === 0 && $election->candidateGroups()->count() === 0) {
            $errors[] = 'Pemilihan harus memiliki setidaknya 1 Posisi Jabatan atau Pasangan Calon.';
        }

        if ($election->voters()->count() === 0) {
            $errors[] = 'Daftar pemilih belum ditambahkan.';
        }

        if ($election->start_at->gte($election->end_at)) {
            $errors[] = 'Waktu selesai harus setelah waktu mulai.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
