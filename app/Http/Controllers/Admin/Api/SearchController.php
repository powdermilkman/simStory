<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\ChoiceOption;
use App\Models\ContentTrigger;
use App\Models\Post;
use App\Models\Phase;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function characters(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $limit = min($request->input('limit', 20), 50);

        $characters = Character::query()
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('username', 'like', "%{$query}%")
                      ->orWhere('display_name', 'like', "%{$query}%");
                });
            })
            ->orderBy('display_name')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $characters->map(fn($c) => [
                'id' => $c->id,
                'display_name' => $c->display_name,
                'username' => $c->username,
                'avatar_url' => $c->avatar_path ? Storage::url($c->avatar_path) : null,
                'initial' => strtoupper(substr($c->display_name, 0, 1)),
                'role' => $c->role?->name,
            ])
        ]);
    }

    public function threads(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $limit = min($request->input('limit', 20), 50);

        $threads = Thread::query()
            ->with('category')
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $threads->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'category' => $t->category?->name,
                'slug' => $t->slug,
            ])
        ]);
    }

    public function posts(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $threadId = $request->input('thread_id');
        $limit = min($request->input('limit', 20), 50);

        $posts = Post::query()
            ->with(['thread', 'author'])
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($threadId, fn($q) => $q->where('thread_id', $threadId))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $posts->map(fn($p) => [
                'id' => $p->id,
                'name' => 'Post #' . $p->id,
                'thread_title' => $p->thread?->title,
                'author' => $p->author?->display_name,
                'excerpt' => \Str::limit(strip_tags($p->content), 60),
            ])
        ]);
    }

    public function triggers(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $limit = min($request->input('limit', 20), 50);

        $triggers = ContentTrigger::query()
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('identifier', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $triggers->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'identifier' => $t->identifier,
                'type' => ContentTrigger::TYPES[$t->type] ?? $t->type,
            ])
        ]);
    }

    public function choiceOptions(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $limit = min($request->input('limit', 20), 50);

        $options = ChoiceOption::query()
            ->with('choice')
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where('label', 'like', "%{$query}%")
                  ->orWhereHas('choice', fn($q) => $q->where('prompt_text', 'like', "%{$query}%"));
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $options->map(fn($o) => [
                'id' => $o->id,
                'name' => $o->label,
                'choice_prompt' => $o->choice?->prompt_text,
                'description' => $o->description,
            ])
        ]);
    }

    public function phases(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $id = $request->input('id');
        $limit = min($request->input('limit', 20), 50);

        $phases = Phase::query()
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($query && !$id, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('identifier', 'like', "%{$query}%");
            })
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $phases->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'identifier' => $p->identifier,
            ])
        ]);
    }
}
