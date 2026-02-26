<?php

namespace App\Services;

use App\Models\Phase;
use App\Models\Reader;
use App\Models\ReaderPhaseProgress;

class PhaseProgressService
{
    public function checkProgress(Reader $reader): void
    {
        // Get all active phases the reader hasn't completed
        $phases = Phase::where('is_active', true)
            ->whereDoesntHave('readerProgress', function ($q) use ($reader) {
                $q->where('reader_id', $reader->id)
                    ->where('status', ReaderPhaseProgress::STATUS_COMPLETED);
            })
            ->with(['parentPhase', 'conditions', 'actions'])
            ->orderBy('sort_order')
            ->get();

        foreach ($phases as $phase) {
            $this->checkPhaseProgress($reader, $phase);
        }
    }

    public function checkPhaseProgress(Reader $reader, Phase $phase): void
    {
        // Check if phase can be started
        if (!$phase->canStartFor($reader)) {
            return;
        }

        // Ensure progress record exists
        $progress = ReaderPhaseProgress::firstOrCreate(
            ['reader_id' => $reader->id, 'phase_id' => $phase->id],
            ['status' => ReaderPhaseProgress::STATUS_IN_PROGRESS, 'started_at' => now()]
        );

        // If already completed, skip
        if ($progress->status === ReaderPhaseProgress::STATUS_COMPLETED) {
            return;
        }

        // Mark as in progress if not started
        if ($progress->status === ReaderPhaseProgress::STATUS_NOT_STARTED) {
            $progress->markInProgress();
        }

        // Check all conditions
        if ($phase->areConditionsMet($reader)) {
            $this->completePhase($reader, $phase, $progress);
        }
    }

    protected function completePhase(Reader $reader, Phase $phase, ReaderPhaseProgress $progress): void
    {
        // Mark as completed
        $progress->markCompleted();

        // Execute all actions
        foreach ($phase->actions as $action) {
            $action->execute($reader);
        }

        // Recursively check child phases
        foreach ($phase->childPhases()->where('is_active', true)->get() as $child) {
            $this->checkPhaseProgress($reader, $child);
        }

        // Check sibling phases that might now be unblocked
        if ($phase->parent_phase_id) {
            $siblings = Phase::where('parent_phase_id', $phase->parent_phase_id)
                ->where('id', '!=', $phase->id)
                ->where('is_active', true)
                ->where('requires_all_sibling_phases', true)
                ->get();

            foreach ($siblings as $sibling) {
                $this->checkPhaseProgress($reader, $sibling);
            }
        }
    }

    public function getPhaseTree(Reader $reader): array
    {
        $rootPhases = Phase::getRootPhases();

        return $rootPhases->map(function ($phase) use ($reader) {
            return $this->buildPhaseNode($phase, $reader);
        })->toArray();
    }

    protected function buildPhaseNode(Phase $phase, Reader $reader): array
    {
        $progress = $phase->getProgressFor($reader);
        $conditionsTotal = $phase->conditions->count();
        $conditionsMet = $phase->getConditionsMetCount($reader);

        return [
            'id' => $phase->id,
            'name' => $phase->name,
            'identifier' => $phase->identifier,
            'description' => $phase->description,
            'status' => $progress?->status ?? ReaderPhaseProgress::STATUS_NOT_STARTED,
            'can_start' => $phase->canStartFor($reader),
            'conditions_total' => $conditionsTotal,
            'conditions_met' => $conditionsMet,
            'started_at' => $progress?->started_at,
            'completed_at' => $progress?->completed_at,
            'children' => $phase->childPhases->map(function ($child) use ($reader) {
                return $this->buildPhaseNode($child, $reader);
            })->toArray(),
        ];
    }

    public function getCompletedPhaseIds(Reader $reader): array
    {
        return ReaderPhaseProgress::where('reader_id', $reader->id)
            ->where('status', ReaderPhaseProgress::STATUS_COMPLETED)
            ->pluck('phase_id')
            ->toArray();
    }

    public function forceCompletePhase(Reader $reader, Phase $phase): void
    {
        $progress = ReaderPhaseProgress::firstOrCreate(
            ['reader_id' => $reader->id, 'phase_id' => $phase->id],
            ['status' => ReaderPhaseProgress::STATUS_NOT_STARTED]
        );

        if ($progress->status !== ReaderPhaseProgress::STATUS_COMPLETED) {
            $this->completePhase($reader, $phase, $progress);
        }
    }

    public function resetPhaseProgress(Reader $reader, Phase $phase): void
    {
        ReaderPhaseProgress::where('reader_id', $reader->id)
            ->where('phase_id', $phase->id)
            ->delete();
    }

    public function resetAllProgress(Reader $reader): void
    {
        ReaderPhaseProgress::where('reader_id', $reader->id)->delete();
    }
}
