<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReaderProgress extends Model
{
    protected $table = 'reader_progress';

    protected $fillable = [
        'reader_id',
        'choice_option_id',
        'chosen_at',
    ];

    protected $casts = [
        'chosen_at' => 'datetime',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(Reader::class);
    }

    public function choiceOption(): BelongsTo
    {
        return $this->belongsTo(ChoiceOption::class);
    }
}
