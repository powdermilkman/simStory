<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReport;
use App\Services\PhaseProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReaderReportController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $reader = Auth::guard('reader')->user();

        if (!$reader) {
            return back()->with('error', 'You must be logged in to report a post.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|in:' . implode(',', array_keys(PostReport::REASONS)),
            'details' => 'nullable|string|max:1000',
        ]);

        // Check if reader has already reported this post
        $existing = PostReport::where('post_id', $post->id)
            ->where('reader_id', $reader->id)
            ->first();

        if ($existing) {
            // Update existing report
            $existing->update([
                'reason' => $validated['reason'],
                'details' => $validated['details'],
                'status' => PostReport::STATUS_PENDING,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);
        } else {
            // Create new report
            PostReport::create([
                'post_id' => $post->id,
                'reader_id' => $reader->id,
                'reason' => $validated['reason'],
                'details' => $validated['details'],
            ]);
        }

        // Record the action for trigger/phase purposes
        $reader->recordAction('report_post', 'post', $post->id);

        // Check phase progress
        app(PhaseProgressService::class)->checkProgress($reader);

        return back()->with('success', 'Report submitted. Thank you for helping keep our community safe.');
    }
}
