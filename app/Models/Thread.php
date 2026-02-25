<?php

namespace App\Models;

use App\Traits\HasConditionalVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasConditionalVisibility;
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'fake_created_at',
        'is_pinned',
        'is_locked',
        'view_count',
        'phase_id',
    ];

    protected $casts = [
        'fake_created_at' => 'datetime',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'author_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function firstPost()
    {
        if ($this->relationLoaded('posts')) {
            return $this->posts->sortBy('fake_created_at')->first();
        }
        return $this->posts()->oldest('fake_created_at')->first();
    }

    public function lastPost()
    {
        if ($this->relationLoaded('posts')) {
            return $this->posts->sortByDesc('fake_created_at')->first();
        }
        return $this->posts()->latest('fake_created_at')->first();
    }

    public function replyCount(): int
    {
        if ($this->relationLoaded('posts')) {
            return max(0, $this->posts->count() - 1);
        }
        return $this->posts()->count() - 1; // Exclude the first post
    }
}
