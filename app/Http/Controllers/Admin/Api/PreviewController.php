<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class PreviewController extends Controller
{
    /**
     * Preview a post with forum styling.
     * Used by the admin post editor for live preview.
     */
    public function post(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'post_id' => 'nullable|exists:posts,id',
                'author_id' => 'required|exists:characters,id',
                'content' => 'required|string',
                'fake_created_at' => 'nullable|date',
                'fake_edited_at' => 'nullable|date',
            ]);

            // Build a temporary post object for preview
            if (!empty($validated['post_id'])) {
                $post = Post::with(['author.role', 'attachments'])->find($validated['post_id']);
            } else {
                $post = new Post();
                // Set empty attachments collection for new posts
                $post->setRelation('attachments', new Collection());
            }

            // Load the author
            $author = Character::with('role')->find($validated['author_id']);
            $post->author = $author;
            $post->author_id = $author->id;

            // Set preview values
            $post->fake_created_at = !empty($validated['fake_created_at'])
                ? now()->parse($validated['fake_created_at'])
                : now();
            $post->fake_edited_at = !empty($validated['fake_edited_at'])
                ? now()->parse($validated['fake_edited_at'])
                : null;

            // Render the post card with preview content
            $html = view('admin.partials.post-preview', [
                'post' => $post,
                'previewContent' => $validated['content'],
            ])->render();

            return response($html)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response('<div class="text-red-500 p-4">Preview error: ' . e($e->getMessage()) . '</div>')
                ->header('Content-Type', 'text/html');
        }
    }
}
