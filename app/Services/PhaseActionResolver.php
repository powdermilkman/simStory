<?php

namespace App\Services;

use App\Models\Character;
use App\Models\PhaseAction;
use App\Models\Reader;
use App\Models\ReaderPhaseProgress;
use Illuminate\Support\Collection;

class PhaseActionResolver
{
    protected array $completedPhaseIdsCache = [];
    protected array $characterValueCache = [];

    /**
     * Get the completed phase IDs for a reader (cached per-request).
     */
    public function getCompletedPhaseIds(Reader $reader): Collection
    {
        if (!isset($this->completedPhaseIdsCache[$reader->id])) {
            $this->completedPhaseIdsCache[$reader->id] = ReaderPhaseProgress::where('reader_id', $reader->id)
                ->where('status', ReaderPhaseProgress::STATUS_COMPLETED)
                ->pluck('phase_id');
        }

        return $this->completedPhaseIdsCache[$reader->id];
    }

    /**
     * Get the effective value of a character field for a reader.
     * Applies all modify_character actions from completed phases in order.
     */
    public function getEffectiveCharacterValue(Character $character, string $field, ?Reader $reader): mixed
    {
        // If no reader, return the character's base value
        if (!$reader) {
            return $character->$field;
        }

        // Check cache first
        $cacheKey = "{$character->id}_{$field}_{$reader->id}";
        if (isset($this->characterValueCache[$cacheKey])) {
            return $this->characterValueCache[$cacheKey];
        }

        // Get completed phase IDs for this reader
        $completedPhaseIds = $this->getCompletedPhaseIds($reader);
        if ($completedPhaseIds->isEmpty()) {
            return $character->$field;
        }

        // Get all modify_character actions from completed phases that target this character and field
        $actions = PhaseAction::where('type', PhaseAction::TYPE_MODIFY_CHARACTER)
            ->where('target_type', 'character')
            ->where('target_id', $character->id)
            ->whereIn('phase_id', $completedPhaseIds)
            ->with('phase')
            ->get()
            ->filter(function ($action) use ($field) {
                $data = $action->action_data ?? [];
                return ($data['field'] ?? null) === $field;
            })
            ->sortBy(fn($action) => [$action->phase->sort_order, $action->sort_order]);

        // If no matching actions, return base value
        if ($actions->isEmpty()) {
            return $character->$field;
        }

        // Apply operations in order
        $value = $character->$field;
        foreach ($actions as $action) {
            $value = $this->applyOperation($value, $action, $field);
        }

        // Cache the result
        $this->characterValueCache[$cacheKey] = $value;

        return $value;
    }

    /**
     * Apply a single action's operation to a value.
     */
    protected function applyOperation(mixed $currentValue, PhaseAction $action, string $field): mixed
    {
        $data = $action->action_data ?? [];
        $newValue = $data['value'] ?? null;

        // Handle bytes with special clamping and add/set operations
        if ($field === 'bytes') {
            $operation = $data['bytes_operation'] ?? 'set';
            $amount = (int) $newValue;

            if ($operation === 'add') {
                return max(0, min(5, (int)$currentValue + $amount));
            }
            // set operation
            return max(0, min(5, $amount));
        }

        // For boolean fields like is_official
        if ($field === 'is_official') {
            return (bool) $newValue;
        }

        // For all other fields, just set the new value
        return $newValue;
    }

    /**
     * Clear the cache (useful for testing or when data changes).
     */
    public function clearCache(): void
    {
        $this->completedPhaseIdsCache = [];
        $this->characterValueCache = [];
    }
}
