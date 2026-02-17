<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Choice extends Model
{
    public const TYPE_CHOICE = 'choice';
    public const TYPE_POLL = 'poll';

    public const TYPES = [
        self::TYPE_CHOICE => 'Choice (branching story)',
        self::TYPE_POLL => 'Poll (show results)',
    ];

    protected $fillable = [
        'trigger_post_id',
        'prompt_text',
        'type',
        'total_votes',
        'description',
        'identifier',
    ];

    protected $casts = [
        'total_votes' => 'integer',
    ];

    public function triggerPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'trigger_post_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ChoiceOption::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    public function isPoll(): bool
    {
        return $this->type === self::TYPE_POLL;
    }

    public function isChoice(): bool
    {
        return $this->type === self::TYPE_CHOICE;
    }
}
