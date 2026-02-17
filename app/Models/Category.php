<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
