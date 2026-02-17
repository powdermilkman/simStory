<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CharacterController extends Controller
{
    public function index()
    {
        $characters = Character::with('role')->orderBy('username')->get();

        return view('admin.characters.index', compact('characters'));
    }

    public function create()
    {
        $roles = Role::orderBy('sort_order')->get();
        return view('admin.characters.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:characters',
            'display_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'signature' => 'nullable|string|max:500',
            'fake_join_date' => 'nullable|date',
            'role_id' => 'nullable|exists:roles,id',
            'is_official' => 'boolean',
            'show_bytes' => 'boolean',
            'bytes' => 'nullable|integer|min:0|max:5',
            'bio' => 'nullable|string',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        unset($validated['avatar']);
        $validated['post_count'] = 0;
        $validated['is_official'] = $request->boolean('is_official');
        $validated['show_bytes'] = $request->boolean('show_bytes');
        $validated['bytes'] = $request->input('bytes', 0);

        Character::create($validated);

        return redirect()->route('admin.characters.index')
            ->with('success', 'Character created successfully.');
    }

    public function show(Character $character)
    {
        $character->load(['role', 'posts.thread', 'threads.category']);

        return view('admin.characters.show', compact('character'));
    }

    public function edit(Character $character)
    {
        $roles = Role::orderBy('sort_order')->get();
        return view('admin.characters.edit', compact('character', 'roles'));
    }

    public function update(Request $request, Character $character)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:characters,username,' . $character->id,
            'display_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'signature' => 'nullable|string|max:500',
            'fake_join_date' => 'nullable|date',
            'role_id' => 'nullable|exists:roles,id',
            'is_official' => 'boolean',
            'show_bytes' => 'boolean',
            'bytes' => 'nullable|integer|min:0|max:5',
            'bio' => 'nullable|string',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($character->avatar_path) {
                Storage::disk('public')->delete($character->avatar_path);
            }
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        unset($validated['avatar']);
        $validated['is_official'] = $request->boolean('is_official');
        $validated['show_bytes'] = $request->boolean('show_bytes');
        $validated['bytes'] = $request->input('bytes', 0);

        $character->update($validated);

        return redirect()->route('admin.characters.index')
            ->with('success', 'Character updated successfully.');
    }

    public function destroy(Character $character)
    {
        if ($character->avatar_path) {
            Storage::disk('public')->delete($character->avatar_path);
        }

        $character->delete();

        return redirect()->route('admin.characters.index')
            ->with('success', 'Character deleted successfully.');
    }
}
