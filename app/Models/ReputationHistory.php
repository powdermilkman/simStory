<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReputationHistory extends Model
{
    protected $table = 'reputation_history';

    protected $fillable = [
        'character_id',
        'reputation_type_id',
        'previous_value',
        'new_value',
        'change_amount',
        'reason',
        'phase_id',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function reputationType(): BelongsTo
    {
        return $this->belongsTo(ReputationType::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }
}
