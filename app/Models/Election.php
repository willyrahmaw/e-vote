<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'created_by',
        'name',
        'slug',
        'description',
        'instructions',
        'start_at',
        'end_at',
        'status',
        'result_visibility',
        'is_public',
        'is_live_result_enabled',
        'allow_abstain',
    ];

    protected function casts(): array
    {
        return [
            'status' => ElectionStatus::class,
            'result_visibility' => ResultVisibility::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_public' => 'boolean',
            'is_live_result_enabled' => 'boolean',
            'allow_abstain' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(ElectionPosition::class)->orderBy('sort_order');
    }

    public function candidateEntries(): HasMany
    {
        return $this->hasMany(CandidateEntry::class);
    }

    public function candidateGroups(): HasMany
    {
        return $this->hasMany(CandidateGroup::class);
    }

    public function voters(): HasMany
    {
        return $this->hasMany(ElectionVoter::class);
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(Ballot::class);
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('status', ElectionStatus::Active);
    }

    public function scopePublicVisible(Builder $query): void
    {
        $query->where('is_public', true);
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === ElectionStatus::Draft;
    }

    public function isScheduled(): bool
    {
        return $this->status === ElectionStatus::Scheduled;
    }

    public function isActive(): bool
    {
        return $this->status === ElectionStatus::Active;
    }

    public function isEnded(): bool
    {
        return $this->status === ElectionStatus::Ended;
    }

    public function isCancelled(): bool
    {
        return $this->status === ElectionStatus::Cancelled;
    }

    public function isWithinVotingPeriod(): bool
    {
        $now = now();
        return $now->gte($this->start_at) && $now->lte($this->end_at);
    }
}
