<?php

namespace App\Services\Candidate;

use App\Models\Candidate;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\CandidateGroupMember;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    public function uploadPhoto(UploadedFile $file, string $folder = 'candidates'): string
    {
        return $file->store($folder, 'public');
    }

    public function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
