<?php

namespace App\Models;

use App\Traits\HasConditionalVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessage extends Model
{
    use HasConditionalVisibility;
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'content',
        'fake_sent_at',
        'is_read',
        'is_inbox_message',
        'phase_id',
    ];

    protected $casts = [
        'fake_sent_at' => 'datetime',
        'is_read' => 'boolean',
        'is_inbox_message' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'recipient_id');
    }

    public function scopeInboxMessages($query)
    {
        return $query->where('is_inbox_message', true);
    }

    public function scopeArchivedMessages($query)
    {
        return $query->where('is_inbox_message', false)->whereNotNull('recipient_id');
    }

}
