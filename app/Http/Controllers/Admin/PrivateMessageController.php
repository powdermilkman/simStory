<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class PrivateMessageController extends Controller
{
    public function index()
    {
        $messages = PrivateMessage::with(['sender', 'recipient'])
            ->latest('fake_sent_at')
            ->paginate(config('pagination.admin'));

        return view('admin.private-messages.index', compact('messages'));
    }

    public function create()
    {
        $characters = Character::orderBy('username')->get();

        return view('admin.private-messages.create', compact('characters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:characters,id',
            'recipient_id' => 'nullable|exists:characters,id|different:sender_id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'fake_sent_at' => 'nullable|date',
            'is_read' => 'boolean',
            'is_inbox_message' => 'boolean',
            'phase_id' => 'nullable|exists:phases,id',
        ]);

        $validated['is_read'] = $request->boolean('is_read');
        $validated['is_inbox_message'] = $request->boolean('is_inbox_message');

        // If inbox message, recipient is not required
        if ($validated['is_inbox_message']) {
            $validated['recipient_id'] = null;
        }

        PrivateMessage::create($validated);

        return redirect()->route('admin.private-messages.index')
            ->with('success', 'Private message created successfully.');
    }

    public function show(PrivateMessage $privateMessage)
    {
        $privateMessage->load(['sender', 'recipient', 'phase']);

        return view('admin.private-messages.show', compact('privateMessage'));
    }

    public function edit(PrivateMessage $privateMessage)
    {
        $characters = Character::orderBy('username')->get();

        return view('admin.private-messages.edit', compact('privateMessage', 'characters'));
    }

    public function update(Request $request, PrivateMessage $privateMessage)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:characters,id',
            'recipient_id' => 'nullable|exists:characters,id|different:sender_id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'fake_sent_at' => 'nullable|date',
            'is_read' => 'boolean',
            'is_inbox_message' => 'boolean',
            'phase_id' => 'nullable|exists:phases,id',
        ]);

        $validated['is_read'] = $request->boolean('is_read');
        $validated['is_inbox_message'] = $request->boolean('is_inbox_message');

        // If inbox message, recipient is not required
        if ($validated['is_inbox_message']) {
            $validated['recipient_id'] = null;
        }

        $privateMessage->update($validated);

        return redirect()->route('admin.private-messages.show', $privateMessage)
            ->with('success', 'Private message updated successfully.');
    }

    public function destroy(PrivateMessage $privateMessage)
    {
        $privateMessage->delete();

        return redirect()->route('admin.private-messages.index')
            ->with('success', 'Private message deleted successfully.');
    }
}
