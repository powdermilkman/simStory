<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriggerCondition extends Model
{
    protected $fillable = [
        'content_trigger_id',
        'type',
        'target_type',
        'target_id',
        'choice_option_id',
    ];

    public const TYPE_VIEW_POST = 'view_post';
    public const TYPE_VIEW_THREAD = 'view_thread';
    public const TYPE_REACT_POST = 'react_post';
    public const TYPE_CHOICE = 'choice';
    public const TYPE_REPORT_POST = 'report_post';

    public const TYPES = [
        self::TYPE_VIEW_POST => 'View Post',
        self::TYPE_VIEW_THREAD => 'View Thread',
        self::TYPE_REACT_POST => 'React to Post',
        self::TYPE_CHOICE => 'Make a Choice',
        self::TYPE_REPORT_POST => 'Report Post',
    ];

    public function contentTrigger(): BelongsTo
    {
        return $this->belongsTo(ContentTrigger::class);
    }

    public function choiceOption(): BelongsTo
    {
        return $this->belongsTo(ChoiceOption::class);
    }

    /**
     * Check if this condition is met by the given reader
     */
    public function isMetBy(Reader $reader): bool
    {
        if ($this->type === self::TYPE_CHOICE && $this->choice_option_id) {
            return $reader->hasChosenOption($this->choice_option_id);
        }

        if (in_array($this->type, [self::TYPE_VIEW_POST, self::TYPE_VIEW_THREAD, self::TYPE_REACT_POST])
            && $this->target_type && $this->target_id) {
            return $reader->actions()
                ->where('action_type', $this->type)
                ->where('target_type', $this->target_type)
                ->where('target_id', $this->target_id)
                ->exists();
        }

        if ($this->type === self::TYPE_REPORT_POST && $this->target_type === 'post' && $this->target_id) {
            return PostReport::where('reader_id', $reader->id)
                ->where('post_id', $this->target_id)
                ->exists();
        }

        return false;
    }

    /**
     * Get a human-readable description of this condition
     */
    public function getDescription(): string
    {
        $typeLabel = self::TYPES[$this->type] ?? $this->type;
        
        if ($this->type === self::TYPE_CHOICE) {
            $optionLabel = $this->choiceOption?->label ?? 'Unknown';
            $choiceTitle = $this->choiceOption?->choice?->prompt_text ?? 'Unknown';
            return "{$typeLabel}: \"{$optionLabel}\" (from \"{$choiceTitle}\")";
        }

        return "{$typeLabel}: {$this->target_type} #{$this->target_id}";
    }
}
