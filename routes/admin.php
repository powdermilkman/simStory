<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CharacterController;

use App\Http\Controllers\Admin\ContentTriggerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PrivateMessageController;
use App\Http\Controllers\Admin\ReaderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StoryFlowController;
use App\Http\Controllers\Admin\ThreadController;
use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\PhaseController;
use App\Http\Controllers\Admin\ThreadComposerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Api\SearchController;
use App\Http\Controllers\Admin\Api\PreviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Thread Composer - MUST come before resource routes to avoid {thread} capturing "compose"
    Route::get('threads/compose', [ThreadComposerController::class, 'create'])->name('threads.compose');
    Route::post('threads/compose', [ThreadComposerController::class, 'store'])->name('threads.compose.store');

    Route::resource('categories', CategoryController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('characters', CharacterController::class);
    Route::resource('threads', ThreadController::class);
    Route::resource('posts', PostController::class);
    Route::resource('triggers', ContentTriggerController::class);
    Route::resource('private-messages', PrivateMessageController::class);
    Route::resource('readers', ReaderController::class)->only(['index', 'show', 'destroy']);
    Route::resource('phases', PhaseController::class);
    Route::resource('users', UserController::class)->except(['show']);

    // Nested routes for posts within threads
    Route::get('threads/{thread}/posts/create', [PostController::class, 'create'])->name('threads.posts.create');
    Route::post('threads/{thread}/posts', [PostController::class, 'store'])->name('threads.posts.store');
    
    // Attachment routes
    Route::post('posts/{post}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    
    // Story Flow Editor routes
    Route::prefix('story-flow')->name('story-flow.')->group(function () {
        Route::get('/', [StoryFlowController::class, 'index'])->name('index');
        Route::get('/data', [StoryFlowController::class, 'data'])->name('data');
        Route::get('/timeline', [StoryFlowController::class, 'timeline'])->name('timeline');
        Route::post('/node', [StoryFlowController::class, 'storeNode'])->name('node.store');
        Route::put('/node/{type}/{id}', [StoryFlowController::class, 'updateNode'])->name('node.update');
        Route::delete('/node/{type}/{id}', [StoryFlowController::class, 'destroyNode'])->name('node.destroy');
        Route::post('/edge', [StoryFlowController::class, 'storeEdge'])->name('edge.store');
        Route::delete('/edge', [StoryFlowController::class, 'destroyEdge'])->name('edge.destroy');
    });

    // Search API routes for searchable dropdowns
    Route::prefix('api/search')->name('api.search.')->group(function () {
        Route::get('/characters', [SearchController::class, 'characters'])->name('characters');
        Route::get('/threads', [SearchController::class, 'threads'])->name('threads');
        Route::get('/posts', [SearchController::class, 'posts'])->name('posts');
        Route::get('/triggers', [SearchController::class, 'triggers'])->name('triggers');
        Route::get('/choice-options', [SearchController::class, 'choiceOptions'])->name('choice-options');
        Route::get('/phases', [SearchController::class, 'phases'])->name('phases');
    });

    // Preview API routes
    Route::prefix('api/preview')->name('api.preview.')->group(function () {
        Route::post('/post', [PreviewController::class, 'post'])->name('post');
    });
});
