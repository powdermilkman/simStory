<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use App\Models\ChoiceOption;
use App\Models\Post;
use App\Models\Character;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChoiceController extends Controller
{
    public function index()
    {
        $choices = Choice::with(['triggerPost.thread', 'options'])
            ->latest()
            ->get();

        return view('admin.choices.index', compact('choices'));
    }

    public function create()
    {
        $posts = Post::with('thread')->get();
        $threads = Thread::orderBy('title')->get();
        $characters = Character::orderBy('username')->get();
        $types = Choice::TYPES;

        return view('admin.choices.create', compact('posts', 'threads', 'characters', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trigger_post_id' => 'nullable|exists:posts,id',
            'prompt_text' => 'required|string|max:255',
            'type' => 'required|string|in:choice,poll',
            'total_votes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'identifier' => 'nullable|string|max:255|unique:choices',
            'options' => 'required|array|min:2',
            'options.*.label' => 'required|string|max:255',
            'options.*.description' => 'nullable|string',
            'options.*.vote_percentage' => 'nullable|integer|min:0|max:100',
            'options.*.result_votes' => 'nullable|array',
            'options.*.spawned_post_id' => 'nullable|exists:posts,id',
        ]);

        $validated['identifier'] = $validated['identifier'] ?? Str::slug($validated['prompt_text']) . '-' . Str::random(4);

        $choice = Choice::create([
            'trigger_post_id' => $validated['trigger_post_id'],
            'prompt_text' => $validated['prompt_text'],
            'type' => $validated['type'],
            'total_votes' => $validated['total_votes'] ?? 0,
            'description' => $validated['description'],
            'identifier' => $validated['identifier'],
        ]);

        foreach ($validated['options'] as $index => $optionData) {
            $choice->options()->create([
                'label' => $optionData['label'],
                'description' => $optionData['description'] ?? null,
                'vote_percentage' => $optionData['vote_percentage'] ?? 0,
                'result_votes' => $optionData['result_votes'] ?? null,
                'spawned_post_id' => $optionData['spawned_post_id'] ?? null,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.choices.show', $choice)
            ->with('success', 'Choice created successfully.');
    }

    public function show(Choice $choice)
    {
        $choice->load(['triggerPost.thread', 'options' => function ($query) {
            $query->with('spawnedPost.thread')
                ->withCount(['unlockedThreads', 'unlockedPosts', 'unlockedMessages']);
        }]);

        return view('admin.choices.show', compact('choice'));
    }

    public function edit(Choice $choice)
    {
        $posts = Post::with('thread')->get();
        $threads = Thread::orderBy('title')->get();
        $characters = Character::orderBy('username')->get();
        $types = Choice::TYPES;
        $choice->load('options');

        return view('admin.choices.edit', compact('choice', 'posts', 'threads', 'characters', 'types'));
    }

    public function update(Request $request, Choice $choice)
    {
        $validated = $request->validate([
            'trigger_post_id' => 'nullable|exists:posts,id',
            'prompt_text' => 'required|string|max:255',
            'type' => 'required|string|in:choice,poll',
            'total_votes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'identifier' => 'required|string|max:255|unique:choices,identifier,' . $choice->id,
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|exists:choice_options,id',
            'options.*.label' => 'required|string|max:255',
            'options.*.description' => 'nullable|string',
            'options.*.vote_percentage' => 'nullable|integer|min:0|max:100',
            'options.*.result_votes' => 'nullable|array',
            'options.*.spawned_post_id' => 'nullable|exists:posts,id',
        ]);

        $choice->update([
            'trigger_post_id' => $validated['trigger_post_id'],
            'prompt_text' => $validated['prompt_text'],
            'type' => $validated['type'],
            'total_votes' => $validated['total_votes'] ?? 0,
            'description' => $validated['description'],
            'identifier' => $validated['identifier'],
        ]);

        // Track existing option IDs
        $existingOptionIds = $choice->options()->pluck('id')->toArray();
        $updatedOptionIds = [];

        foreach ($validated['options'] as $index => $optionData) {
            if (!empty($optionData['id'])) {
                // Update existing option
                $option = ChoiceOption::find($optionData['id']);
                $option->update([
                    'label' => $optionData['label'],
                    'description' => $optionData['description'] ?? null,
                    'vote_percentage' => $optionData['vote_percentage'] ?? 0,
                    'result_votes' => $optionData['result_votes'] ?? null,
                    'spawned_post_id' => $optionData['spawned_post_id'] ?? null,
                    'sort_order' => $index,
                ]);
                $updatedOptionIds[] = $optionData['id'];
            } else {
                // Create new option
                $newOption = $choice->options()->create([
                    'label' => $optionData['label'],
                    'description' => $optionData['description'] ?? null,
                    'vote_percentage' => $optionData['vote_percentage'] ?? 0,
                    'result_votes' => $optionData['result_votes'] ?? null,
                    'spawned_post_id' => $optionData['spawned_post_id'] ?? null,
                    'sort_order' => $index,
                ]);
                $updatedOptionIds[] = $newOption->id;
            }
        }

        // Delete removed options
        $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
        ChoiceOption::whereIn('id', $optionsToDelete)->delete();

        return redirect()->route('admin.choices.show', $choice)
            ->with('success', 'Choice updated successfully.');
    }

    public function destroy(Choice $choice)
    {
        $choice->delete();

        return redirect()->route('admin.choices.index')
            ->with('success', 'Choice deleted successfully.');
    }
}
