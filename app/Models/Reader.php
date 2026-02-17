<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Reader extends Authenticatable
{
    use Notifiable;

    /**
     * Cache for chosen option IDs within a request
     */
    protected ?array $cachedChosenOptionIds = null;

    /**
     * Cache for completed trigger IDs within a request
     */
    protected ?array $cachedCompletedTriggerIds = null;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ReaderProgress::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ReaderAction::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(ReaderVisit::class);
    }

    public function phaseProgress(): HasMany
    {
        return $this->hasMany(ReaderPhaseProgress::class);
    }

    public function hasCompletedPhase(Phase $phase): bool
    {
        return $this->phaseProgress()
            ->where('phase_id', $phase->id)
            ->where('status', ReaderPhaseProgress::STATUS_COMPLETED)
            ->exists();
    }

    public function recordVisit(string $type, int $id): ReaderVisit
    {
        return ReaderVisit::recordVisit($this, $type, $id);
    }

    public function getLastVisit(string $type, int $id): ?ReaderVisit
    {
        return ReaderVisit::getLastVisit($this, $type, $id);
    }

    public function hasChosenOption(int $choiceOptionId): bool
    {
        return $this->progress()->where('choice_option_id', $choiceOptionId)->exists();
    }

    public function hasCompletedChoice(Choice $choice): bool
    {
        return $this->progress()
            ->whereIn('choice_option_id', $choice->options()->pluck('id'))
            ->exists();
    }

    public function getChosenOptionForChoice(Choice $choice): ?ChoiceOption
    {
        $progress = $this->progress()
            ->whereIn('choice_option_id', $choice->options()->pluck('id'))
            ->first();

        return $progress?->choiceOption;
    }

    public function getChosenOptionIds(): array
    {
        if ($this->cachedChosenOptionIds === null) {
            $this->cachedChosenOptionIds = $this->progress()->pluck('choice_option_id')->toArray();
        }

        return $this->cachedChosenOptionIds;
    }

    public function hasCompletedTrigger(ContentTrigger $trigger): bool
    {
        return $trigger->isCompletedBy($this);
    }

    public function recordAction(string $actionType, string $targetType, int $targetId): ReaderAction
    {
        return ReaderAction::record($this, $actionType, $targetType, $targetId);
    }

    /**
     * Get IDs of all completed triggers
     * A trigger is completed when ALL of its conditions are met
     * Cached within the request to avoid repeated queries
     */
    public function getCompletedTriggerIds(): array
    {
        if ($this->cachedCompletedTriggerIds === null) {
            $completedIds = [];

            // Get all triggers with their conditions
            $triggers = ContentTrigger::with('conditions')->get();

            foreach ($triggers as $trigger) {
                if ($trigger->isCompletedBy($this)) {
                    $completedIds[] = $trigger->id;
                }
            }

            $this->cachedCompletedTriggerIds = $completedIds;
        }

        return $this->cachedCompletedTriggerIds;
    }

    /**
     * Clear the cached data (useful after making a choice or completing a trigger)
     */
    public function clearVisibilityCache(): void
    {
        $this->cachedChosenOptionIds = null;
        $this->cachedCompletedTriggerIds = null;
    }
}
