<?php

namespace App\Http\Controllers\Reader;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('reader.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('reader')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('forum.index'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('reader.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:readers',
            'email' => 'required|string|email|max:255|unique:readers',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $reader = Reader::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('reader')->login($reader);

        return redirect(route('forum.index'));
    }

    public function logout(Request $request)
    {
        Auth::guard('reader')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('forum.index'));
    }

    public function progress()
    {
        $reader = Auth::guard('reader')->user();
        $progress = $reader->progress()->with('choiceOption.choice')->get();

        return view('reader.progress', compact('reader', 'progress'));
    }

    public function resetProgress(Request $request)
    {
        $reader = Auth::guard('reader')->user();

        // Delete all progress (choices made)
        $reader->progress()->delete();

        // Delete all actions (views, etc.)
        $reader->actions()->delete();

        // Delete all reactions
        \App\Models\Reaction::where('reader_id', $reader->id)->delete();

        // Delete all visit records (so everything shows as "new" again)
        $reader->visits()->delete();

        // Delete all phase progress
        \App\Models\ReaderPhaseProgress::where('reader_id', $reader->id)->delete();

        return redirect()->route('reader.progress')
            ->with('success', 'Your progress has been reset. You can start fresh!');
    }
}
