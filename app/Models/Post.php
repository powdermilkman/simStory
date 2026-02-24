<?php

namespace App\Models;

use App\Traits\HasConditionalVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasConditionalVisibility;
    protected $fillable = [
        'thread_id',
        'author_id',
        'content',
        'fake_created_at',
        'fake_edited_at',
        'phase_id',
    ];

    protected $casts = [
        'fake_created_at' => 'datetime',
        'fake_edited_at' => 'datetime',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'author_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function choice(): HasOne
    {
        return $this->hasOne(Choice::class, 'trigger_post_id');
    }

    public function reactionCounts(): array
    {
        return $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
}
