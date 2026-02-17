<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    protected $fillable = [
        'post_id',
        'character_id',
        'reader_id',
        'type',
        'fake_created_at',
    ];

    protected $casts = [
        'fake_created_at' => 'datetime',
    ];

    public const TYPES = [
        'like' => '👍',
        'love' => '❤️',
        'insightful' => '💡',
        'amusing' => '😄',
        'agree' => '✅',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function getEmojiAttribute(): string
    {
        return self::TYPES[$this->type] ?? '👍';
    }
}
