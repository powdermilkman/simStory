<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChoiceOption extends Model
{
    protected $fillable = [
        'choice_id',
        'label',
        'description',
        'vote_percentage',
        'result_votes',
        'spawned_post_id',
        'sort_order',
    ];

    protected $casts = [
        'vote_percentage' => 'integer',
        'result_votes' => 'array',
    ];

    /**
     * Get the vote count for a specific option index when this option is chosen
     */
    public function getResultVoteFor(int $optionIndex): int
    {
        if (!$this->result_votes) {
            return 0;
        }
        // Handle both string and int keys from JSON
        return $this->result_votes[$optionIndex] ?? $this->result_votes[(string)$optionIndex] ?? 0;
    }

    /**
     * Get total votes when this option is chosen
     */
    public function getResultTotalVotes(): int
    {
        if (!$this->result_votes) {
            return 0;
        }
        return array_sum($this->result_votes);
    }

    /**
     * Get percentage for a specific option index when this option is chosen
     */
    public function getResultPercentageFor(int $optionIndex): float
    {
        $total = $this->getResultTotalVotes();
        if ($total === 0) {
            return 0;
        }
        $votes = $this->getResultVoteFor($optionIndex);
        return round(($votes / $total) * 100, 1);
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(Choice::class);
    }

    public function readerProgress(): HasMany
    {
        return $this->hasMany(ReaderProgress::class);
    }

    // The post that gets spawned/shown when this option is chosen
    public function spawnedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'spawned_post_id');
    }

}
