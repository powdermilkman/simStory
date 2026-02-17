<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\ChoiceOption;
use App\Models\ContentTrigger;
use App\Models\Phase;
use App\Models\PhaseAction;
use App\Models\PhaseCondition;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhaseController extends Controller
{
    public function index()
    {
        $phases = Phase::with(['parentPhase', 'childPhases', 'conditions', 'actions'])
            ->whereNull('parent_phase_id')
            ->orderBy('sort_order')
            ->get();

        $allPhases = Phase::withCount(['conditions', 'actions', 'readerProgress'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.phases.index', compact('phases', 'allPhases'));
    }

    public function create()
    {
        $parentPhases = Phase::orderBy('sort_order')->get();
        $triggers = ContentTrigger::orderBy('name')->get();
        $threads = Thread::orderBy('title')->get();
        $posts = Post::with('thread')->orderBy('id')->get();
        $choiceOptions = ChoiceOption::with('choice')->get();
        $characters = Character::orderBy('display_name')->get();
        $privateMessages = PrivateMessage::orderBy('subject')->get();

        return view('admin.phases.create', compact(
            'parentPhases',
            'triggers',
            'threads',
            'posts',
            'choiceOptions',
            'characters',
            'privateMessages'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:255|unique:phases,identifier',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'parent_phase_id' => 'nullable|exists:phases,id',
            'requires_all_sibling_phases' => 'boolean',
        ]);

        if (empty($validated['identifier'])) {
            $validated['identifier'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['requires_all_sibling_phases'] = $request->boolean('requires_all_sibling_phases');
        $validated['sort_order'] = $validated['sort_order'] ?? Phase::max('sort_order') + 1;

        $phase = Phase::create($validated);

        // Handle conditions
        if ($request->has('conditions')) {
            foreach ($request->input('conditions', []) as $index => $conditionData) {
                if (empty($conditionData['type'])) continue;

                PhaseCondition::create([
                    'phase_id' => $phase->id,
                    'type' => $conditionData['type'],
                    'target_type' => $conditionData['target_type'] ?? null,
                    'target_id' => $conditionData['target_id'] ?? null,
                    'choice_option_id' => $conditionData['choice_option_id'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // Handle actions
        if ($request->has('actions')) {
            foreach ($request->input('actions', []) as $index => $actionData) {
                if (empty($actionData['type'])) continue;

                PhaseAction::create([
                    'phase_id' => $phase->id,
                    'type' => $actionData['type'],
                    'target_type' => $actionData['target_type'] ?? null,
                    'target_id' => $actionData['target_id'] ?? null,
                    'action_data' => $actionData['action_data'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.phases.index')
            ->with('success', 'Phase created successfully.');
    }

    public function show(Phase $phase)
    {
        $phase->load(['parentPhase', 'childPhases', 'conditions', 'actions', 'readerProgress.reader']);

        $completedCount = $phase->readerProgress->where('status', 'completed')->count();
        $inProgressCount = $phase->readerProgress->where('status', 'in_progress')->count();

        return view('admin.phases.show', compact('phase', 'completedCount', 'inProgressCount'));
    }

    public function edit(Phase $phase)
    {
        $phase->load(['conditions', 'actions']);

        $parentPhases = Phase::where('id', '!=', $phase->id)->orderBy('sort_order')->get();
        $triggers = ContentTrigger::orderBy('name')->get();
        $threads = Thread::orderBy('title')->get();
        $posts = Post::with('thread')->orderBy('id')->get();
        $choiceOptions = ChoiceOption::with('choice')->get();
        $characters = Character::orderBy('display_name')->get();
        $privateMessages = PrivateMessage::orderBy('subject')->get();
        $allPhases = Phase::where('id', '!=', $phase->id)->orderBy('sort_order')->get();

        return view('admin.phases.edit', compact(
            'phase',
            'parentPhases',
            'triggers',
            'threads',
            'posts',
            'choiceOptions',
            'characters',
            'privateMessages',
            'allPhases'
        ));
    }

    public function update(Request $request, Phase $phase)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:255|unique:phases,identifier,' . $phase->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'parent_phase_id' => 'nullable|exists:phases,id',
            'requires_all_sibling_phases' => 'boolean',
        ]);

        // Prevent circular parent reference
        if ($validated['parent_phase_id'] == $phase->id) {
            $validated['parent_phase_id'] = null;
        }

        if (empty($validated['identifier'])) {
            $validated['identifier'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['requires_all_sibling_phases'] = $request->boolean('requires_all_sibling_phases');

        $phase->update($validated);

        // Update conditions - delete existing and recreate
        $phase->conditions()->delete();
        if ($request->has('conditions')) {
            foreach ($request->input('conditions', []) as $index => $conditionData) {
                if (empty($conditionData['type'])) continue;

                PhaseCondition::create([
                    'phase_id' => $phase->id,
                    'type' => $conditionData['type'],
                    'target_type' => $conditionData['target_type'] ?? null,
                    'target_id' => $conditionData['target_id'] ?? null,
                    'choice_option_id' => $conditionData['choice_option_id'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // Update actions - delete existing and recreate
        $phase->actions()->delete();
        if ($request->has('actions')) {
            foreach ($request->input('actions', []) as $index => $actionData) {
                if (empty($actionData['type'])) continue;

                PhaseAction::create([
                    'phase_id' => $phase->id,
                    'type' => $actionData['type'],
                    'target_type' => $actionData['target_type'] ?? null,
                    'target_id' => $actionData['target_id'] ?? null,
                    'action_data' => $actionData['action_data'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.phases.index')
            ->with('success', 'Phase updated successfully.');
    }

    public function destroy(Phase $phase)
    {
        $phase->delete();

        return redirect()->route('admin.phases.index')
            ->with('success', 'Phase deleted successfully.');
    }
}
