<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReaderPhaseProgress extends Model
{
    protected $table = 'reader_phase_progress';

    protected $fillable = [
        'reader_id',
        'phase_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_NOT_STARTED => 'Not Started',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function markInProgress(): void
    {
        if ($this->status === self::STATUS_NOT_STARTED) {
            $this->update([
                'status' => self::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]);
        }
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'started_at' => $this->started_at ?? now(),
            'completed_at' => now(),
        ]);
    }
}
