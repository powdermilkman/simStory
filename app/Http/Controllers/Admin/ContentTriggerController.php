<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChoiceOption;
use App\Models\ContentTrigger;
use App\Models\Post;
use App\Models\Thread;
use App\Models\TriggerCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentTriggerController extends Controller
{
    public function index()
    {
        $triggers = ContentTrigger::with('conditions')->orderBy('name')->paginate(config('pagination.admin'));
        return view('admin.triggers.index', compact('triggers'));
    }

    public function create()
    {
        $threads = Thread::orderBy('title')->get();
        $posts = Post::with('thread')->get();
        $choiceOptions = ChoiceOption::with('choice')->get();
        $types = TriggerCondition::TYPES;

        return view('admin.triggers.create', compact('threads', 'posts', 'choiceOptions', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:255|unique:content_triggers,identifier',
            'description' => 'nullable|string',
            'conditions' => 'required|array|min:1',
            'conditions.*.type' => 'required|string|in:' . implode(',', array_keys(TriggerCondition::TYPES)),
            'conditions.*.target_id' => 'nullable|integer',
            'conditions.*.choice_option_id' => 'nullable|exists:choice_options,id',
        ]);

        // Generate identifier if not provided
        $identifier = $validated['identifier'] ?? Str::slug($validated['name']) . '-' . Str::random(6);

        $trigger = ContentTrigger::create([
            'name' => $validated['name'],
            'identifier' => $identifier,
            'description' => $validated['description'] ?? null,
        ]);

        // Create conditions
        foreach ($validated['conditions'] as $conditionData) {
            $targetType = match($conditionData['type']) {
                TriggerCondition::TYPE_VIEW_POST, TriggerCondition::TYPE_REACT_POST => 'post',
                TriggerCondition::TYPE_VIEW_THREAD => 'thread',
                default => null,
            };

            $trigger->conditions()->create([
                'type' => $conditionData['type'],
                'target_type' => $targetType,
                'target_id' => $conditionData['target_id'] ?? null,
                'choice_option_id' => $conditionData['choice_option_id'] ?? null,
            ]);
        }

        return redirect()->route('admin.triggers.index')
            ->with('success', 'Content trigger created successfully.');
    }

    public function show(ContentTrigger $trigger)
    {
        $trigger->load(['conditions.choiceOption.choice', 'unlockedThreads', 'unlockedPosts', 'unlockedMessages']);
        return view('admin.triggers.show', compact('trigger'));
    }

    public function edit(ContentTrigger $trigger)
    {
        $trigger->load('conditions.choiceOption.choice');
        $threads = Thread::orderBy('title')->get();
        $posts = Post::with('thread')->get();
        $choiceOptions = ChoiceOption::with('choice')->get();
        $types = TriggerCondition::TYPES;

        return view('admin.triggers.edit', compact('trigger', 'threads', 'posts', 'choiceOptions', 'types'));
    }

    public function update(Request $request, ContentTrigger $trigger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:content_triggers,identifier,' . $trigger->id,
            'description' => 'nullable|string',
            'conditions' => 'required|array|min:1',
            'conditions.*.id' => 'nullable|exists:trigger_conditions,id',
            'conditions.*.type' => 'required|string|in:' . implode(',', array_keys(TriggerCondition::TYPES)),
            'conditions.*.target_id' => 'nullable|integer',
            'conditions.*.choice_option_id' => 'nullable|exists:choice_options,id',
        ]);

        $trigger->update([
            'name' => $validated['name'],
            'identifier' => $validated['identifier'],
            'description' => $validated['description'] ?? null,
        ]);

        // Track which condition IDs we're keeping
        $existingIds = $trigger->conditions()->pluck('id')->toArray();
        $updatedIds = [];

        foreach ($validated['conditions'] as $conditionData) {
            $targetType = match($conditionData['type']) {
                TriggerCondition::TYPE_VIEW_POST, TriggerCondition::TYPE_REACT_POST => 'post',
                TriggerCondition::TYPE_VIEW_THREAD => 'thread',
                default => null,
            };

            if (!empty($conditionData['id'])) {
                // Update existing condition
                $condition = TriggerCondition::find($conditionData['id']);
                $condition->update([
                    'type' => $conditionData['type'],
                    'target_type' => $targetType,
                    'target_id' => $conditionData['target_id'] ?? null,
                    'choice_option_id' => $conditionData['choice_option_id'] ?? null,
                ]);
                $updatedIds[] = $conditionData['id'];
            } else {
                // Create new condition
                $newCondition = $trigger->conditions()->create([
                    'type' => $conditionData['type'],
                    'target_type' => $targetType,
                    'target_id' => $conditionData['target_id'] ?? null,
                    'choice_option_id' => $conditionData['choice_option_id'] ?? null,
                ]);
                $updatedIds[] = $newCondition->id;
            }
        }

        // Delete removed conditions
        $toDelete = array_diff($existingIds, $updatedIds);
        TriggerCondition::whereIn('id', $toDelete)->delete();

        return redirect()->route('admin.triggers.index')
            ->with('success', 'Content trigger updated successfully.');
    }

    public function destroy(ContentTrigger $trigger)
    {
        $trigger->delete();

        return redirect()->route('admin.triggers.index')
            ->with('success', 'Content trigger deleted successfully.');
    }
}
