<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateGroup extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'election_id',
        'name',
        'number',
        'logo',
        'slogan',
        'vision',
        'mission',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CandidateGroupMember::class)->orderBy('sort_order');
    }

    public function ballotChoices(): HasMany
    {
        return $this->hasMany(BallotChoice::class, 'candidate_group_id');
    }
}
