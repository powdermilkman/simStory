<?php

namespace App\Http\Controllers;

use App\Models\ContentTrigger;
use App\Models\Post;
use App\Models\Reaction;
use App\Services\PhaseProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReaderReactionController extends Controller
{
    public function toggle(Request $request, Post $post)
    {
        $reader = Auth::guard('reader')->user();
        
        if (!$reader) {
            return response()->json(['error' => 'Must be logged in to react'], 401);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(Reaction::TYPES)),
        ]);

        // One reaction per post — find any existing reaction from this reader
        $existing = Reaction::where('post_id', $post->id)
            ->where('reader_id', $reader->id)
            ->first();

        if ($existing && $existing->type === $validated['type']) {
            // Same type — toggle off
            $existing->delete();
            $reacted = false;
        } else {
            // Different type or no existing — replace / add
            if ($existing) {
                $existing->delete();
            }

            Reaction::create([
                'post_id' => $post->id,
                'reader_id' => $reader->id,
                'character_id' => null,
                'type' => $validated['type'],
            ]);
            $reacted = true;

            // Record the action for trigger purposes
            $reader->recordAction(ContentTrigger::TYPE_REACT_POST, 'post', $post->id);

            // Check phase progress after reaction
            app(PhaseProgressService::class)->checkProgress($reader);
        }

        // Get updated counts
        $counts = $post->fresh()->reactionCounts();

        return response()->json([
            'reacted' => $reacted,
            'counts' => $counts,
        ]);
    }

    public function status(Post $post)
    {
        $reader = Auth::guard('reader')->user();
        
        $readerReactions = [];
        if ($reader) {
            $readerReactions = Reaction::where('post_id', $post->id)
                ->where('reader_id', $reader->id)
                ->pluck('type')
                ->toArray();
        }

        return response()->json([
            'counts' => $post->reactionCounts(),
            'reader_reactions' => $readerReactions,
        ]);
    }
}
