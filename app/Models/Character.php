<?php

namespace App\Models;

use App\Services\PhaseActionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    protected $fillable = [
        'username',
        'display_name',
        'avatar_path',
        'signature',
        'fake_join_date',
        'role_title',
        'role_id',
        'is_official',
        'post_count',
        'bio',
        'bytes',
        'show_bytes',
    ];

    protected $casts = [
        'fake_join_date' => 'datetime',
        'is_official' => 'boolean',
        'show_bytes' => 'boolean',
        'bytes' => 'integer',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'author_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'recipient_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Set the bytes value (clamped to 0-5) - global default
     */
    public function setBytes(int $value): void
    {
        $this->update(['bytes' => max(0, min(5, $value))]);
    }

    /**
     * Modify the bytes value by a delta (clamped to 0-5) - global default
     */
    public function modifyBytes(int $delta): void
    {
        $newValue = max(0, min(5, $this->bytes + $delta));
        $this->update(['bytes' => $newValue]);
    }

    /**
     * Get the effective value of a field for a specific reader.
     * Computed dynamically based on completed phase actions.
     */
    public function getEffectiveValue(string $field, ?Reader $reader): mixed
    {
        return app(PhaseActionResolver::class)
            ->getEffectiveCharacterValue($this, $field, $reader);
    }

    /**
     * Get effective bytes for a reader
     */
    public function getEffectiveBytes(?Reader $reader): int
    {
        return (int) $this->getEffectiveValue('bytes', $reader);
    }

    /**
     * Get effective role_title for a reader
     */
    public function getEffectiveRoleTitle(?Reader $reader): ?string
    {
        return $this->getEffectiveValue('role_title', $reader);
    }

    /**
     * Get effective signature for a reader
     */
    public function getEffectiveSignature(?Reader $reader): ?string
    {
        return $this->getEffectiveValue('signature', $reader);
    }

    /**
     * Get effective bio for a reader
     */
    public function getEffectiveBio(?Reader $reader): ?string
    {
        return $this->getEffectiveValue('bio', $reader);
    }

    /**
     * Get effective is_official for a reader
     */
    public function getEffectiveIsOfficial(?Reader $reader): bool
    {
        return (bool) $this->getEffectiveValue('is_official', $reader);
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function updatePostCount(): void
    {
        $this->update(['post_count' => $this->posts()->count()]);
    }
}
