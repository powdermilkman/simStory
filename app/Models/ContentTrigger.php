<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentTrigger extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'type',
        'target_type',
        'target_id',
        'choice_option_id',
        'description',
    ];

    // Trigger types (kept for reference, actual types are on TriggerCondition)
    public const TYPE_VIEW_POST = 'view_post';
    public const TYPE_VIEW_THREAD = 'view_thread';
    public const TYPE_REACT_POST = 'react_post';
    public const TYPE_CHOICE = 'choice';

    public const TYPES = [
        self::TYPE_VIEW_POST => 'View a Post',
        self::TYPE_VIEW_THREAD => 'View a Thread',
        self::TYPE_REACT_POST => 'React to a Post',
        self::TYPE_CHOICE => 'Make a Choice',
    ];

    /**
     * Get all conditions for this trigger
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(TriggerCondition::class);
    }

    /**
     * Legacy: single choice option (for backward compatibility)
     */
    public function choiceOption(): BelongsTo
    {
        return $this->belongsTo(ChoiceOption::class);
    }

    /**
     * Get the target model (Post or Thread) - legacy support
     */
    public function target()
    {
        if ($this->target_type === 'post') {
            return Post::find($this->target_id);
        } elseif ($this->target_type === 'thread') {
            return Thread::find($this->target_id);
        }
        return null;
    }

    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    /**
     * Check if a reader has completed ALL conditions for this trigger
     */
    public function isCompletedBy(?Reader $reader): bool
    {
        if (!$reader) {
            return false;
        }

        // If we have conditions in the new table, use those
        if ($this->conditions()->exists()) {
            foreach ($this->conditions as $condition) {
                if (!$condition->isMetBy($reader)) {
                    return false;
                }
            }
            return true;
        }

        // Legacy fallback: use the old single-condition fields
        if ($this->type === self::TYPE_CHOICE) {
            return $reader->hasChosenOption($this->choice_option_id);
        }

        if ($this->type && $this->target_type && $this->target_id) {
            return ReaderAction::where('reader_id', $reader->id)
                ->where('action_type', $this->type)
                ->where('target_type', $this->target_type)
                ->where('target_id', $this->target_id)
                ->exists();
        }

        return false;
    }

    /**
     * Get the count of conditions met by a reader
     */
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

    /**
     * Get a summary of the trigger's conditions
     */
    public function getConditionsSummary(): string
    {
        $conditions = $this->conditions;
        
        if ($conditions->isEmpty()) {
            // Legacy fallback
            if ($this->type) {
                return TriggerCondition::TYPES[$this->type] ?? $this->type;
            }
            return 'No conditions';
        }

        if ($conditions->count() === 1) {
            return $conditions->first()->getDescription();
        }

        return $conditions->count() . ' conditions (all required)';
    }
}
