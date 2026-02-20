<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Choice;
use App\Models\ChoiceOption;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['thread', 'author'])
            ->latest()
            ->paginate(config('pagination.admin'));

        return view('admin.posts.index', compact('posts'));
    }

    public function create(?Thread $thread = null)
    {
        return view('admin.posts.create', compact('thread'));
    }

    public function store(Request $request, ?Thread $thread = null)
    {
        $validated = $request->validate([
            'thread_id' => $thread ? 'nullable' : 'required|exists:threads,id',
            'author_id' => 'required|exists:characters,id',
            'content' => 'required|string',
            'fake_created_at' => 'nullable|date',
            'fake_edited_at' => 'nullable|date',
            'phase_id' => 'nullable|exists:phases,id',
            'has_choice' => 'nullable|string',
            'choice_type' => 'nullable|string|in:choice,poll',
            'choice_prompt_text' => 'nullable|string|max:255',
            'choice_identifier' => 'nullable|string|max:255',
            'choice_description' => 'nullable|string',
            'choice_options' => 'nullable|array',
            'choice_options.*.label' => 'nullable|string|max:255',
            'choice_options.*.description' => 'nullable|string',
            'choice_options.*.result_votes' => 'nullable|array',
            'choice_options.*.spawned_post_id' => 'nullable|exists:posts,id',
        ]);

        if ($thread) {
            $validated['thread_id'] = $thread->id;
        }

        $post = Post::create([
            'thread_id' => $validated['thread_id'],
            'author_id' => $validated['author_id'],
            'content' => $validated['content'],
            'fake_created_at' => $validated['fake_created_at'] ?? null,
            'fake_edited_at' => $validated['fake_edited_at'] ?? null,
            'phase_id' => $validated['phase_id'] ?? null,
        ]);

        // Update character post count
        Character::find($validated['author_id'])->updatePostCount();

        // Handle choice/poll
        $this->syncChoice($post, $validated);

        $targetThread = $thread ?? Thread::find($validated['thread_id']);

        return redirect()->route('admin.threads.show', $targetThread)
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->load(['thread', 'author', 'attachments', 'choice.options']);

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $post->load(['attachments', 'choice.options']);

        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'author_id' => 'required|exists:characters,id',
            'content' => 'required|string',
            'fake_created_at' => 'nullable|date',
            'fake_edited_at' => 'nullable|date',
            'phase_id' => 'nullable|exists:phases,id',
            'has_choice' => 'nullable|string',
            'choice_id' => 'nullable|integer',
            'choice_type' => 'nullable|string|in:choice,poll',
            'choice_prompt_text' => 'nullable|string|max:255',
            'choice_identifier' => 'nullable|string|max:255',
            'choice_description' => 'nullable|string',
            'choice_options' => 'nullable|array',
            'choice_options.*.id' => 'nullable|integer',
            'choice_options.*.label' => 'nullable|string|max:255',
            'choice_options.*.description' => 'nullable|string',
            'choice_options.*.result_votes' => 'nullable|array',
            'choice_options.*.spawned_post_id' => 'nullable|exists:posts,id',
        ]);

        $oldAuthorId = $post->author_id;
        $post->update([
            'author_id' => $validated['author_id'],
            'content' => $validated['content'],
            'fake_created_at' => $validated['fake_created_at'] ?? null,
            'fake_edited_at' => $validated['fake_edited_at'] ?? null,
            'phase_id' => $validated['phase_id'] ?? null,
        ]);

        // Update post counts if author changed
        if ($oldAuthorId != $validated['author_id']) {
            Character::find($oldAuthorId)->updatePostCount();
            Character::find($validated['author_id'])->updatePostCount();
        }

        // Handle choice/poll
        $this->syncChoice($post, $validated);

        return redirect()->route('admin.threads.show', $post->thread)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $thread = $post->thread;
        $authorId = $post->author_id;

        $post->delete();

        // Update character post count
        Character::find($authorId)->updatePostCount();

        return redirect()->route('admin.threads.show', $thread)
            ->with('success', 'Post deleted successfully.');
    }

    private function syncChoice(Post $post, array $validated): void
    {
        $hasChoice = ($validated['has_choice'] ?? '0') === '1';
        $existingChoice = $post->choice;

        if (!$hasChoice) {
            // Remove choice if it exists
            if ($existingChoice) {
                $existingChoice->options()->delete();
                $existingChoice->delete();
            }
            return;
        }

        // Need prompt text for a choice
        if (empty($validated['choice_prompt_text'])) {
            return;
        }

        $choiceData = [
            'trigger_post_id' => $post->id,
            'prompt_text' => $validated['choice_prompt_text'],
            'type' => $validated['choice_type'] ?? 'choice',
            'description' => $validated['choice_description'] ?? null,
            'identifier' => $validated['choice_identifier'] ?: Str::slug($validated['choice_prompt_text']) . '-' . Str::random(4),
        ];

        if ($existingChoice) {
            $existingChoice->update($choiceData);
            $choice = $existingChoice;
        } else {
            $choice = Choice::create($choiceData);
        }

        // Sync options
        $existingOptionIds = $choice->options()->pluck('id')->toArray();
        $updatedOptionIds = [];

        foreach (($validated['choice_options'] ?? []) as $index => $optData) {
            $optionFields = [
                'label' => $optData['label'],
                'description' => $optData['description'] ?? null,
                'result_votes' => $optData['result_votes'] ?? null,
                'spawned_post_id' => !empty($optData['spawned_post_id']) ? $optData['spawned_post_id'] : null,
                'sort_order' => $index,
            ];

            if (!empty($optData['id']) && in_array($optData['id'], $existingOptionIds)) {
                ChoiceOption::where('id', $optData['id'])->update($optionFields);
                $updatedOptionIds[] = $optData['id'];
            } else {
                $newOpt = $choice->options()->create($optionFields);
                $updatedOptionIds[] = $newOpt->id;
            }
        }

        // Delete removed options
        $toDelete = array_diff($existingOptionIds, $updatedOptionIds);
        if ($toDelete) {
            ChoiceOption::whereIn('id', $toDelete)->delete();
        }
    }
}
