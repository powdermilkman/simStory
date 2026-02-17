<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReaderVisit extends Model
{
    protected $fillable = [
        'reader_id',
        'visitable_type',
        'visitable_id',
        'last_visited_at',
    ];

    protected $casts = [
        'last_visited_at' => 'datetime',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record or update a visit for a reader
     */
    public static function recordVisit(Reader $reader, string $type, int $id): self
    {
        return self::updateOrCreate(
            [
                'reader_id' => $reader->id,
                'visitable_type' => $type,
                'visitable_id' => $id,
            ],
            [
                'last_visited_at' => now(),
            ]
        );
    }

    /**
     * Get the last visit time for a reader to a specific item
     */
    public static function getLastVisit(Reader $reader, string $type, int $id): ?self
    {
        return self::where('reader_id', $reader->id)
            ->where('visitable_type', $type)
            ->where('visitable_id', $id)
            ->first();
    }
}
