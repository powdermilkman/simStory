<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReaderAction extends Model
{
    protected $fillable = [
        'reader_id',
        'action_type',
        'target_type',
        'target_id',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    // Get the target model
    public function target()
    {
        if ($this->target_type === 'post') {
            return Post::find($this->target_id);
        } elseif ($this->target_type === 'thread') {
            return Thread::find($this->target_id);
        }
        return null;
    }

    // Record an action for a reader
    public static function record(Reader $reader, string $actionType, string $targetType, int $targetId): self
    {
        return self::firstOrCreate(
            [
                'reader_id' => $reader->id,
                'action_type' => $actionType,
                'target_type' => $targetType,
                'target_id' => $targetId,
            ],
            [
                'performed_at' => now(),
            ]
        );
    }
}
