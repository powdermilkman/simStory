<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phase extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'description',
        'sort_order',
        'is_active',
        'parent_phase_id',
        'requires_all_sibling_phases',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_all_sibling_phases' => 'boolean',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(PhaseCondition::class)->orderBy('sort_order');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(PhaseAction::class)->orderBy('sort_order');
    }

    public function parentPhase(): BelongsTo
    {
        return $this->belongsTo(Phase::class, 'parent_phase_id');
    }

    public function childPhases(): HasMany
    {
        return $this->hasMany(Phase::class, 'parent_phase_id')->orderBy('sort_order');
    }

    public function readerProgress(): HasMany
    {
        return $this->hasMany(ReaderPhaseProgress::class);
    }

    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    public function isCompletedBy(Reader $reader): bool
    {
        $progress = $this->readerProgress()->where('reader_id', $reader->id)->first();
        return $progress?->status === ReaderPhaseProgress::STATUS_COMPLETED;
    }

    public function getProgressFor(Reader $reader): ?ReaderPhaseProgress
    {
        return $this->readerProgress()->where('reader_id', $reader->id)->first();
    }

    public function canStartFor(Reader $reader): bool
    {
        // Must be active
        if (!$this->is_active) {
            return false;
        }

        // Check if parent phase (if any) is complete
        if ($this->parent_phase_id) {
            if (!$this->parentPhase->isCompletedBy($reader)) {
                return false;
            }
        }

        // Check if required sibling phases are complete
        if ($this->requires_all_sibling_phases && $this->parent_phase_id) {
            $siblings = Phase::where('parent_phase_id', $this->parent_phase_id)
                ->where('sort_order', '<', $this->sort_order)
                ->where('is_active', true)
                ->get();

            foreach ($siblings as $sibling) {
                if (!$sibling->isCompletedBy($reader)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function areConditionsMet(Reader $reader): bool
    {
        if ($this->conditions->isEmpty()) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (!$condition->isMetBy($reader)) {
                return false;
            }
        }

        return true;
    }

    public function getConditionsMetCount(Reader $reader): int
    {
        $count = 0;
        foreach ($this->conditions as $condition) {
            if ($condition->isMetBy($reader)) {
                $count++;
            }
        }
        return $count;
    }

    public static function getRootPhases()
    {
        return static::whereNull('parent_phase_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
