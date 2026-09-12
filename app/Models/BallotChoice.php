<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BallotChoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ballot_id',
        'position_id',
        'candidate_entry_id',
        'candidate_group_id',
        'option_id',
        'is_abstain',
    ];

    protected function casts(): array
    {
        return [
            'is_abstain' => 'boolean',
        ];
    }

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id');
    }

    public function candidateEntry(): BelongsTo
    {
        return $this->belongsTo(CandidateEntry::class, 'candidate_entry_id');
    }

    public function candidateGroup(): BelongsTo
    {
        return $this->belongsTo(CandidateGroup::class, 'candidate_group_id');
    }
}
