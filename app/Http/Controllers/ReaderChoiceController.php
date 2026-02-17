<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\ReaderProgress;
use App\Services\PhaseProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReaderChoiceController extends Controller
{
    public function makeChoice(Request $request, Choice $choice)
    {
        $reader = Auth::guard('reader')->user();

        // Check if reader has already made this choice
        if ($reader->hasCompletedChoice($choice)) {
            return back()->with('error', 'You have already made this choice.');
        }

        $validated = $request->validate([
            'option_id' => 'required|exists:choice_options,id',
        ]);

        // Verify the option belongs to this choice
        $option = $choice->options()->where('id', $validated['option_id'])->first();
        if (!$option) {
            return back()->with('error', 'Invalid option selected.');
        }

        // Record the choice
        ReaderProgress::create([
            'reader_id' => $reader->id,
            'choice_option_id' => $option->id,
            'chosen_at' => now(),
        ]);

        // Clear visibility cache so new content becomes visible
        $reader->clearVisibilityCache();

        // Check phase progress after making a choice
        app(PhaseProgressService::class)->checkProgress($reader);

        return back()->with('success', 'Choice recorded! New content may now be available.');
    }
}
