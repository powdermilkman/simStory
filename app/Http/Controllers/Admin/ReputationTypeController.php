<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReputationType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReputationTypeController extends Controller
{
    public function index()
    {
        $reputationTypes = ReputationType::withCount('characterReputations')
            ->orderBy('sort_order')
            ->get();
        return view('admin.reputation-types.index', compact('reputationTypes'));
    }

    public function create()
    {
        return view('admin.reputation-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:255|unique:reputation_types,identifier',
            'description' => 'nullable|string',
            'min_value' => 'required|integer',
            'max_value' => 'required|integer|gte:min_value',
            'default_value' => 'required|integer',
            'is_visible_to_readers' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['identifier'])) {
            $validated['identifier'] = Str::slug($validated['name']);
        }

        $validated['is_visible_to_readers'] = $request->boolean('is_visible_to_readers');
        $validated['sort_order'] = $validated['sort_order'] ?? ReputationType::max('sort_order') + 1;

        // Clamp default value
        $validated['default_value'] = max(
            $validated['min_value'],
            min($validated['max_value'], $validated['default_value'])
        );

        ReputationType::create($validated);

        return redirect()->route('admin.reputation-types.index')
            ->with('success', 'Reputation type created successfully.');
    }

    public function edit(ReputationType $reputationType)
    {
        return view('admin.reputation-types.edit', compact('reputationType'));
    }

    public function update(Request $request, ReputationType $reputationType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'nullable|string|max:255|unique:reputation_types,identifier,' . $reputationType->id,
            'description' => 'nullable|string',
            'min_value' => 'required|integer',
            'max_value' => 'required|integer|gte:min_value',
            'default_value' => 'required|integer',
            'is_visible_to_readers' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['identifier'])) {
            $validated['identifier'] = Str::slug($validated['name']);
        }

        $validated['is_visible_to_readers'] = $request->boolean('is_visible_to_readers');

        // Clamp default value
        $validated['default_value'] = max(
            $validated['min_value'],
            min($validated['max_value'], $validated['default_value'])
        );

        $reputationType->update($validated);

        return redirect()->route('admin.reputation-types.index')
            ->with('success', 'Reputation type updated successfully.');
    }

    public function destroy(ReputationType $reputationType)
    {
        $reputationType->delete();

        return redirect()->route('admin.reputation-types.index')
            ->with('success', 'Reputation type deleted successfully.');
    }
}
