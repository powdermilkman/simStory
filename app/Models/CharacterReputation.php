<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterReputation extends Model
{
    protected $fillable = [
        'character_id',
        'reputation_type_id',
        'value',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function reputationType(): BelongsTo
    {
        return $this->belongsTo(ReputationType::class);
    }

    public function modifyValue(int $delta, string $reason, ?int $phaseId = null): void
    {
        $previous = $this->value;
        $this->value = $this->reputationType->clampValue($this->value + $delta);
        $this->save();

        ReputationHistory::create([
            'character_id' => $this->character_id,
            'reputation_type_id' => $this->reputation_type_id,
            'previous_value' => $previous,
            'new_value' => $this->value,
            'change_amount' => $delta,
            'reason' => $reason,
            'phase_id' => $phaseId,
        ]);
    }

    public function setValue(int $value, string $reason, ?int $phaseId = null): void
    {
        $previous = $this->value;
        $this->value = $this->reputationType->clampValue($value);
        $this->save();

        ReputationHistory::create([
            'character_id' => $this->character_id,
            'reputation_type_id' => $this->reputation_type_id,
            'previous_value' => $previous,
            'new_value' => $this->value,
            'change_amount' => $this->value - $previous,
            'reason' => $reason,
            'phase_id' => $phaseId,
        ]);
    }
}
