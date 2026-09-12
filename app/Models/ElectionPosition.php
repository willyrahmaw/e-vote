<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionPosition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'election_id',
        'name',
        'description',
        'min_choices',
        'max_choices',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_choices' => 'integer',
            'max_choices' => 'integer',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function candidateEntries(): HasMany
    {
        return $this->hasMany(CandidateEntry::class, 'position_id');
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(CandidateGroupMember::class, 'position_id');
    }

    public function ballotChoices(): HasMany
    {
        return $this->hasMany(BallotChoice::class, 'position_id');
    }
}
