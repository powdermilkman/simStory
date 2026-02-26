<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Character;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::with(['category', 'author', 'phase'])
            ->latest()
            ->paginate(config('pagination.admin'));

        return view('admin.threads.index', compact('threads'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        $characters = Character::orderBy('username')->get();

        return view('admin.threads.create', compact('categories', 'characters'));
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
            'first_post_content' => 'required|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['is_locked'] = $request->boolean('is_locked');

        $firstPostContent = $validated['first_post_content'];
        unset($validated['first_post_content']);

        $thread = Thread::create($validated);

        // Create the first post
        $thread->posts()->create([
            'author_id' => $validated['author_id'],
            'content' => $firstPostContent,
            'fake_created_at' => $validated['fake_created_at'],
        ]);

        // Update character post count
        Character::find($validated['author_id'])?->updatePostCount();

        return redirect()->route('admin.threads.show', $thread)
            ->with('success', 'Thread created successfully.');
    }

    public function show(Thread $thread)
    {
        $thread->load(['category', 'author', 'posts.author', 'posts.choice', 'posts.phase']);

        return view('admin.threads.show', compact('thread'));
    }

    public function edit(Thread $thread)
    {
        $categories = Category::orderBy('sort_order')->get();
        $characters = Character::orderBy('username')->get();

        return view('admin.threads.edit', compact('thread', 'categories', 'characters'));
    }

    public function update(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:characters,id',
            'title' => 'required|string|max:255',
            'fake_created_at' => 'nullable|date',
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'phase_id' => 'nullable|exists:phases,id',
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['is_locked'] = $request->boolean('is_locked');

        $thread->update($validated);

        return redirect()->route('admin.threads.show', $thread)
            ->with('success', 'Thread updated successfully.');
    }

    public function destroy(Thread $thread)
    {
        $thread->delete();

        return redirect()->route('admin.threads.index')
            ->with('success', 'Thread deleted successfully.');
    }
}
