<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReputationType extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'description',
        'min_value',
        'max_value',
        'default_value',
        'is_visible_to_readers',
        'sort_order',
    ];

    protected $casts = [
        'is_visible_to_readers' => 'boolean',
    ];

    public function characterReputations(): HasMany
    {
        return $this->hasMany(CharacterReputation::class);
    }

    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    public function clampValue(int $value): int
    {
        return max($this->min_value, min($this->max_value, $value));
    }
}
