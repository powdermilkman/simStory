<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        $readers = Reader::withCount([
                'progress',
                'actions',
                'phaseProgress as phases_completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->latest()
            ->paginate(config('pagination.admin'));

        return view('admin.readers.index', compact('readers'));
    }

    public function show(Reader $reader)
    {
        $reader->load(['progress.choiceOption.choice', 'actions', 'phaseProgress.phase']);

        // Batch-load thread targets for view_thread actions to avoid N+1
        $threadIds = $reader->actions->where('action_type', 'view_thread')->pluck('target_id')->unique();
        $actionThreads = \App\Models\Thread::whereIn('id', $threadIds)->get()->keyBy('id');

        // Get reader's reactions
        $reactions = \App\Models\Reaction::where('reader_id', $reader->id)
            ->with('post.thread')
            ->latest()
            ->get();

        return view('admin.readers.show', compact('reader', 'reactions', 'actionThreads'));
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return redirect()->route('admin.readers.index')
            ->with('success', 'Reader deleted successfully.');
    }
}
