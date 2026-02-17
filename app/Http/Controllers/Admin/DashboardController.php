<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Character;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Reader;
use App\Models\Thread;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'categories' => Category::count(),
            'characters' => Character::count(),
            'threads' => Thread::count(),
            'posts' => Post::count(),
            'private_messages' => PrivateMessage::count(),
            'readers' => Reader::count(),
        ];

        $recentThreads = Thread::with(['author', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $recentPosts = Post::with(['author', 'thread'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentThreads', 'recentPosts'));
    }
}
