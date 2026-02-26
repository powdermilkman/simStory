<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reader\AuthController as ReaderAuthController;
use App\Http\Controllers\ReaderChoiceController;
use App\Http\Controllers\ReaderReactionController;
use App\Http\Controllers\ReaderReportController;
use Illuminate\Support\Facades\Route;

// Public forum routes
Route::name('forum.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [ForumController::class, 'category'])->name('category');
    Route::get('/category/{category:slug}/{thread:slug}', [ForumController::class, 'thread'])->name('thread');
    Route::get('/member/{character:username}', [ForumController::class, 'profile'])->name('profile');
    Route::get('/messages', [ForumController::class, 'messages'])->name('messages');
    Route::get('/messages/{message}', [ForumController::class, 'message'])->name('message');
});

// Reader authentication routes
Route::prefix('reader')->name('reader.')->group(function () {
    Route::middleware('guest:reader')->group(function () {
        Route::get('/login', [ReaderAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [ReaderAuthController::class, 'login'])->middleware('throttle:6,1');
        Route::get('/register', [ReaderAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [ReaderAuthController::class, 'register']);
    });

    Route::middleware('auth:reader')->group(function () {
        Route::post('/logout', [ReaderAuthController::class, 'logout'])->name('logout');
        Route::get('/progress', [ReaderAuthController::class, 'progress'])->name('progress');
        Route::post('/progress/reset', [ReaderAuthController::class, 'resetProgress'])->name('progress.reset');
    });
});

// Choice selection route (for readers)
Route::post('/choice/{choice}', [ReaderChoiceController::class, 'makeChoice'])
    ->middleware('auth:reader')
    ->name('choice.make');

// Reaction routes (for readers)
Route::post('/posts/{post}/react', [ReaderReactionController::class, 'toggle'])
    ->middleware('auth:reader')
    ->name('posts.react');
Route::get('/posts/{post}/reactions', [ReaderReactionController::class, 'status'])
    ->name('posts.reactions');

// Report routes (for readers)
Route::post('/posts/{post}/report', [ReaderReportController::class, 'store'])
    ->middleware('auth:reader')
    ->name('posts.report');

// Admin dashboard redirect
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin profile management (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
