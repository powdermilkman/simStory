<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'display_type' => 'nullable|in:inline,attachment,gallery',
            'caption' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('attachments', $filename, 'public');

        // Get next sort order
        $maxSortOrder = $post->attachments()->max('sort_order') ?? 0;

        $attachment = $post->attachments()->create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'path' => $path,
            'display_type' => $request->input('display_type', 'attachment'),
            'caption' => $request->input('caption'),
            'sort_order' => $maxSortOrder + 1,
        ]);

        return back()->with('success', 'Attachment uploaded successfully.');
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->path);
        $post = $attachment->post;
        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully.');
    }
}
