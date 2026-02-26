<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseAction extends Model
{
    protected $fillable = [
        'phase_id',
        'type',
        'target_type',
        'target_id',
        'action_data',
        'sort_order',
    ];

    protected $casts = [
        'action_data' => 'array',
    ];

    public const TYPE_MODIFY_CHARACTER = 'modify_character';
    public const TYPE_SEND_MESSAGE = 'send_message';
    public const TYPE_TRIGGER_PHASE = 'trigger_phase';

    public const TYPES = [
        self::TYPE_MODIFY_CHARACTER => 'Modify Character',
        self::TYPE_SEND_MESSAGE => 'Send Message',
        self::TYPE_TRIGGER_PHASE => 'Trigger Another Phase',
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function execute(Reader $reader): void
    {
        switch ($this->type) {
            case self::TYPE_MODIFY_CHARACTER:
                // No-op: Character modifications are computed dynamically at render time
                // by PhaseActionResolver based on completed phases. This allows admin
                // changes to phase actions to propagate to all readers immediately.
                break;

            case self::TYPE_SEND_MESSAGE:
                $this->executeSendMessage($reader);
                break;

            case self::TYPE_TRIGGER_PHASE:
                $this->executeTriggerPhase($reader);
                break;
        }
    }

    protected function executeSendMessage(Reader $reader): void
    {
        $data = $this->action_data ?? [];
        $templateMessageId = $data['private_message_id'] ?? null;

        if (!$templateMessageId) {
            return;
        }

        $template = PrivateMessage::find($templateMessageId);
        if (!$template) {
            return;
        }

        // One inbox message per action — deduplicate so multiple readers completing
        // the same phase don't create duplicate messages.
        $alreadyExists = PrivateMessage::where('is_inbox_message', true)
            ->where('phase_id', $this->phase_id)
            ->where('sender_id', $template->sender_id)
            ->where('subject', $template->subject)
            ->exists();

        if (!$alreadyExists) {
            PrivateMessage::create([
                'sender_id'        => $template->sender_id,
                'recipient_id'     => $template->recipient_id,
                'subject'          => $template->subject,
                'content'          => $template->content,
                'is_inbox_message' => true,
                'is_read'          => false,
                'fake_sent_at'     => $template->fake_sent_at ?? now(),
                'phase_id'         => $this->phase_id,
            ]);
        }
    }

    protected function executeTriggerPhase(Reader $reader): void
    {
        $data = $this->action_data ?? [];
        $phaseId = $data['phase_id'] ?? null;

        if (!$phaseId) {
            return;
        }

        $targetPhase = Phase::find($phaseId);
        if (!$targetPhase || !$targetPhase->is_active) {
            return;
        }

        // Force-complete the target phase
        $progress = ReaderPhaseProgress::firstOrCreate(
            ['reader_id' => $reader->id, 'phase_id' => $targetPhase->id],
            ['status' => ReaderPhaseProgress::STATUS_NOT_STARTED]
        );

        if ($progress->status !== ReaderPhaseProgress::STATUS_COMPLETED) {
            $progress->update([
                'status' => ReaderPhaseProgress::STATUS_COMPLETED,
                'started_at' => $progress->started_at ?? now(),
                'completed_at' => now(),
            ]);

            // Execute actions of the triggered phase
            foreach ($targetPhase->actions as $action) {
                $action->execute($reader);
            }
        }
    }

    public function getDescription(): string
    {
        $typeLabel = self::TYPES[$this->type] ?? $this->type;
        $data = $this->action_data ?? [];

        switch ($this->type) {
            case self::TYPE_MODIFY_CHARACTER:
                $character = Character::find($this->target_id);
                $field = $data['field'] ?? 'unknown';
                $value = $data['value'] ?? '';
                if ($field === 'bytes') {
                    $op = $data['bytes_operation'] ?? 'set';
                    $sign = $op === 'set' ? '=' : ($value >= 0 ? '+' : '');
                    return "{$typeLabel}: {$character?->display_name} bytes {$sign}{$value}";
                }
                return "{$typeLabel}: {$character?->display_name} -> {$field}";

            case self::TYPE_TRIGGER_PHASE:
                $phase = Phase::find($data['phase_id'] ?? 0);
                return "{$typeLabel}: {$phase?->name}";

            case self::TYPE_SEND_MESSAGE:
                $msgId = $data['private_message_id'] ?? null;
                $subject = $msgId ? (PrivateMessage::find($msgId)?->subject ?? 'Unknown') : 'Unknown';
                return "{$typeLabel}: \"{$subject}\"";

            default:
                return "{$typeLabel}: {$this->target_type} #{$this->target_id}";
        }
    }
}
