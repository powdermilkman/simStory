<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Character;
use App\Models\Choice;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThreadComposerController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.threads.compose', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:characters,id',
            'title' => 'required|string|max:255',
            'fake_created_at' => 'nullable|date',
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'phase_id' => 'nullable|exists:phases,id',
            'posts' => 'required|array|min:1',
            'posts.*.author_id' => 'required|exists:characters,id',
            'posts.*.content' => 'required|string',
            'posts.*.fake_created_at' => 'nullable|date',
            'posts.*.phase_id' => 'nullable|exists:phases,id',
            'posts.*.has_choice' => 'nullable|string',
            'posts.*.choice_type' => 'nullable|string|in:choice,poll',
            'posts.*.choice_prompt_text' => 'nullable|string|max:255',
            'posts.*.choice_identifier' => 'nullable|string|max:255',
            'posts.*.choice_description' => 'nullable|string',
            'posts.*.choice_options' => 'nullable|array',
            'posts.*.choice_options.*.label' => 'nullable|string|max:255',
            'posts.*.choice_options.*.description' => 'nullable|string',
            'posts.*.choice_options.*.result_votes' => 'nullable|array',
            'posts.*.choice_options.*.spawned_post_id' => 'nullable|exists:posts,id',
        ]);

        $thread = DB::transaction(function () use ($validated, $request) {
            // Create the thread
            $threadData = [
                'category_id' => $validated['category_id'],
                'author_id' => $validated['author_id'],
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
                'fake_created_at' => $validated['fake_created_at'],
                'is_pinned' => $request->boolean('is_pinned'),
                'is_locked' => $request->boolean('is_locked'),
                'phase_id' => $validated['phase_id'] ?? null,
            ];

            $thread = Thread::create($threadData);

            // Track authors for post count updates
            $authorIds = [];

            // Create all posts
            foreach ($validated['posts'] as $index => $postData) {
                $post = $thread->posts()->create([
                    'author_id' => $postData['author_id'],
                    'content' => $postData['content'],
                    'fake_created_at' => $postData['fake_created_at'] ?? null,
                    'phase_id' => $postData['phase_id'] ?? null,
                ]);

                // Create choice if present
                if (($postData['has_choice'] ?? '0') === '1' && !empty($postData['choice_prompt_text'])) {
                    $choice = Choice::create([
                        'trigger_post_id' => $post->id,
                        'prompt_text' => $postData['choice_prompt_text'],
                        'type' => $postData['choice_type'] ?? 'choice',
                        'description' => $postData['choice_description'] ?? null,
                        'identifier' => $postData['choice_identifier'] ?: Str::slug($postData['choice_prompt_text']) . '-' . Str::random(4),
                    ]);

                    foreach (($postData['choice_options'] ?? []) as $optIdx => $optData) {
                        $choice->options()->create([
                            'label' => $optData['label'],
                            'description' => $optData['description'] ?? null,
                            'result_votes' => $optData['result_votes'] ?? null,
                            'spawned_post_id' => !empty($optData['spawned_post_id']) ? $optData['spawned_post_id'] : null,
                            'sort_order' => $optIdx,
                        ]);
                    }
                }

                $authorIds[] = $postData['author_id'];
            }

            // Update post counts for all authors
            $uniqueAuthorIds = array_unique($authorIds);
            foreach ($uniqueAuthorIds as $authorId) {
                Character::find($authorId)?->updatePostCount();
            }

            return $thread;
        });

        return redirect()->route('admin.threads.show', $thread)
            ->with('success', 'Thread created with ' . count($validated['posts']) . ' posts.');
    }
}
