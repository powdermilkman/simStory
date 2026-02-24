<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseCondition extends Model
{
    protected $fillable = [
        'phase_id',
        'type',
        'target_type',
        'target_id',
        'choice_option_id',
        'sort_order',
    ];

    public const TYPE_TRIGGER = 'trigger';
    public const TYPE_CHOICE = 'choice';
    public const TYPE_VIEW_POST = 'view_post';
    public const TYPE_VIEW_THREAD = 'view_thread';
    public const TYPE_REACT_POST = 'react_post';
    public const TYPE_ALL_POSTS_IN_THREAD = 'all_posts_in_thread';
    public const TYPE_REPORT_POST = 'report_post';
    public const TYPE_PHASE_COMPLETE = 'phase_complete';

    public const TYPES = [
        self::TYPE_TRIGGER => 'Trigger Completed',
        self::TYPE_CHOICE => 'Choice Made',
        self::TYPE_VIEW_POST => 'Post Viewed',
        self::TYPE_VIEW_THREAD => 'Thread Viewed',
        self::TYPE_REACT_POST => 'Post Reacted To',
        self::TYPE_ALL_POSTS_IN_THREAD => 'All Posts in Thread Viewed',
        self::TYPE_REPORT_POST => 'Post Reported',
        self::TYPE_PHASE_COMPLETE => 'Other Phase Complete',
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function choiceOption(): BelongsTo
    {
        return $this->belongsTo(ChoiceOption::class);
    }

    public function isMetBy(Reader $reader): bool
    {
        switch ($this->type) {
            case self::TYPE_CHOICE:
                return $this->choice_option_id && $reader->hasChosenOption($this->choice_option_id);

            case self::TYPE_TRIGGER:
                if ($this->target_type === 'trigger' && $this->target_id) {
                    $trigger = ContentTrigger::find($this->target_id);
                    return $trigger && $trigger->isCompletedBy($reader);
                }
                return false;

            case self::TYPE_VIEW_POST:
            case self::TYPE_VIEW_THREAD:
            case self::TYPE_REACT_POST:
                if ($this->target_type && $this->target_id) {
                    return $reader->actions()
                        ->where('action_type', $this->type)
                        ->where('target_type', $this->target_type)
                        ->where('target_id', $this->target_id)
                        ->exists();
                }
                return false;

            case self::TYPE_ALL_POSTS_IN_THREAD:
                if ($this->target_type === 'thread' && $this->target_id) {
                    $thread = Thread::find($this->target_id);
                    if (!$thread) {
                        return false;
                    }

                    $visiblePosts = $thread->posts()->visibleTo($reader)->pluck('id');
                    if ($visiblePosts->isEmpty()) {
                        return true; // No posts to view
                    }

                    $viewedPosts = $reader->actions()
                        ->where('action_type', 'view_post')
                        ->where('target_type', 'post')
                        ->whereIn('target_id', $visiblePosts)
                        ->pluck('target_id');

                    return $visiblePosts->diff($viewedPosts)->isEmpty();
                }
                return false;

            case self::TYPE_REPORT_POST:
                if ($this->target_type === 'post' && $this->target_id) {
                    return PostReport::where('reader_id', $reader->id)
                        ->where('post_id', $this->target_id)
                        ->exists();
                }
                return false;

            case self::TYPE_PHASE_COMPLETE:
                if ($this->target_type === 'phase' && $this->target_id) {
                    $targetPhase = Phase::find($this->target_id);
                    return $targetPhase && $targetPhase->isCompletedBy($reader);
                }
                return false;

            default:
                return false;
        }
    }

    public function getDescription(): string
    {
        $typeLabel = self::TYPES[$this->type] ?? $this->type;

        switch ($this->type) {
            case self::TYPE_CHOICE:
                $optionLabel = $this->choiceOption?->label ?? 'Unknown';
                return "{$typeLabel}: \"{$optionLabel}\"";

            case self::TYPE_TRIGGER:
                $trigger = ContentTrigger::find($this->target_id);
                return "{$typeLabel}: " . ($trigger?->name ?? "#{$this->target_id}");

            case self::TYPE_PHASE_COMPLETE:
                $phase = Phase::find($this->target_id);
                return "{$typeLabel}: " . ($phase?->name ?? "#{$this->target_id}");

            case self::TYPE_ALL_POSTS_IN_THREAD:
                $thread = Thread::find($this->target_id);
                return "{$typeLabel}: " . ($thread?->title ?? "#{$this->target_id}");

            case self::TYPE_VIEW_POST:
            case self::TYPE_REACT_POST:
            case self::TYPE_REPORT_POST:
                $post = Post::find($this->target_id);
                $title = $post?->thread?->title ?? null;
                $label = $title ? "\"{$title}\" (post #{$this->target_id})" : "post #{$this->target_id}";
                return "{$typeLabel}: {$label}";

            case self::TYPE_VIEW_THREAD:
                $thread = Thread::find($this->target_id);
                return "{$typeLabel}: " . ($thread?->title ?? "thread #{$this->target_id}");

            default:
                return "{$typeLabel}: {$this->target_type} #{$this->target_id}";
        }
    }
}
