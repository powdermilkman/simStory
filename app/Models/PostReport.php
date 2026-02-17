<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReport extends Model
{
    protected $fillable = [
        'post_id',
        'reader_id',
        'reason',
        'details',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public const REASON_SPAM = 'spam';
    public const REASON_INAPPROPRIATE = 'inappropriate';
    public const REASON_MISINFORMATION = 'misinformation';
    public const REASON_HARASSMENT = 'harassment';
    public const REASON_OTHER = 'other';

    public const REASONS = [
        self::REASON_SPAM => 'Spam or advertising',
        self::REASON_INAPPROPRIATE => 'Inappropriate content',
        self::REASON_MISINFORMATION => 'Misinformation',
        self::REASON_HARASSMENT => 'Harassment',
        self::REASON_OTHER => 'Other',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_DISMISSED = 'dismissed';
    public const STATUS_ACTIONED = 'actioned';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_REVIEWED => 'Reviewed',
        self::STATUS_DISMISSED => 'Dismissed',
        self::STATUS_ACTIONED => 'Action Taken',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
