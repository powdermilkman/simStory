<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        $readers = Reader::withCount(['progress', 'actions'])
            ->latest()
            ->paginate(config('pagination.admin'));

        return view('admin.readers.index', compact('readers'));
    }

    public function show(Reader $reader)
    {
        $reader->load(['progress.choiceOption.choice', 'actions']);
        
        // Get reader's reactions
        $reactions = \App\Models\Reaction::where('reader_id', $reader->id)
            ->with('post.thread')
            ->latest()
            ->get();

        return view('admin.readers.show', compact('reader', 'reactions'));
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return redirect()->route('admin.readers.index')
            ->with('success', 'Reader deleted successfully.');
    }
}
