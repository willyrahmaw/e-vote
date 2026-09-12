<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionVoter extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'election_id',
        'user_id',
        'is_eligible',
        'has_voted',
        'voted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_eligible' => 'boolean',
            'has_voted' => 'boolean',
            'voted_at' => 'datetime',
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
