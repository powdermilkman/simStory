<?php

namespace App\Traits;

use App\Models\Phase;
use App\Models\Reader;
use App\Models\ReaderPhaseProgress;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasConditionalVisibility
{
    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function isVisibleToReader(?Reader $reader): bool
    {
        if (!$this->phase_id) {
            return true;
        }

        if (!$reader) {
            return false;
        }

        $progress = $reader->phaseProgress()->where('phase_id', $this->phase_id)->first();

        return $progress && $progress->status !== ReaderPhaseProgress::STATUS_NOT_STARTED;
    }

    public function scopeVisibleTo($query, ?Reader $reader)
    {
        $startedPhaseIds = $reader
            ? $reader->phaseProgress()
                ->where('status', '!=', ReaderPhaseProgress::STATUS_NOT_STARTED)
                ->pluck('phase_id')
                ->toArray()
            : [];

        return $query->where(function ($q) use ($startedPhaseIds) {
            $q->whereNull('phase_id')
              ->orWhereIn('phase_id', $startedPhaseIds);
        });
    }
}
