<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Character;
use App\Models\ContentTrigger;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\ReaderVisit;
use App\Models\Thread;
use App\Services\PhaseProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    protected function getReader()
    {
        return Auth::guard('reader')->user();
    }

    public function index()
    {
        $reader = $this->getReader();

        $categories = Category::withCount(['threads' => function ($query) use ($reader) {
                $query->visibleTo($reader);
            }])
            ->orderBy('sort_order')
            ->get();

        // Calculate new content for each category
        $categoryNewCounts = [];
        if ($reader) {
            foreach ($categories as $category) {
                $lastVisit = $reader->getLastVisit('category', $category->id);
                $lastVisitTime = $lastVisit?->last_visited_at;

                if ($lastVisitTime) {
                    // Count visible threads with new posts since last visit
                    $newThreadCount = Thread::where('category_id', $category->id)
                        ->visibleTo($reader)
                        ->where('created_at', '>', $lastVisitTime)
                        ->count();

                    // Count threads with new visible posts
                    $threadsWithNewPosts = Thread::where('category_id', $category->id)
                        ->visibleTo($reader)
                        ->whereHas('posts', function ($q) use ($lastVisitTime, $reader) {
                            $q->where('created_at', '>', $lastVisitTime)
                                ->visibleTo($reader);
                        })
                        ->count();

                    $categoryNewCounts[$category->id] = [
                        'new_threads' => $newThreadCount,
                        'threads_with_new_posts' => $threadsWithNewPosts,
                    ];
                } else {
                    // Never visited - everything is new
                    $categoryNewCounts[$category->id] = [
                        'new_threads' => $category->threads_count,
                        'threads_with_new_posts' => 0,
                    ];
                }
            }
        }

        return view('forum.index', compact('categories', 'categoryNewCounts'));
    }

    public function category(Category $category)
    {
        $reader = $this->getReader();

        $threads = $category->threads()
            ->with(['author.role', 'posts'])
            ->visibleTo($reader)
            ->orderByDesc('is_pinned')
            ->orderByDesc('fake_created_at')
            ->paginate(config('pagination.forum'));

        // Calculate new posts and visible post counts for each thread
        $threadNewCounts = [];
        $threadVisiblePostCounts = [];

        foreach ($threads as $thread) {
            // Base query for visible posts only
            $visiblePostsQuery = $thread->posts()->visibleTo($reader);

            // Count visible posts for reply count display
            $threadVisiblePostCounts[$thread->id] = $visiblePostsQuery->count();

            if ($reader) {
                $lastVisit = $reader->getLastVisit('thread', $thread->id);
                $lastVisitTime = $lastVisit?->last_visited_at;

                if ($lastVisitTime) {
                    $newPostCount = (clone $visiblePostsQuery)
                        ->where('created_at', '>', $lastVisitTime)
                        ->count();

                    $threadNewCounts[$thread->id] = $newPostCount;
                } else {
                    // Never visited - all visible posts are new
                    $threadNewCounts[$thread->id] = $threadVisiblePostCounts[$thread->id];
                }
            }
        }

        if ($reader) {
            // Record visit to this category
            $reader->recordVisit('category', $category->id);
        }

        return view('forum.category', compact('category', 'threads', 'threadNewCounts', 'threadVisiblePostCounts'));
    }

    public function thread(Category $category, Thread $thread)
    {
        $reader = $this->getReader();

        // Check if thread is accessible
        if (!$thread->isVisibleToReader($reader)) {
            abort(404);
        }

        // Record the view action for logged-in readers
        if ($reader) {
            $reader->recordAction(ContentTrigger::TYPE_VIEW_THREAD, 'thread', $thread->id);
        }

        $posts = $thread->posts()
            ->with(['author.role', 'reactions', 'attachments', 'choice.options'])
            ->visibleTo($reader)
            ->orderBy('fake_created_at')
            ->get();

        // Record view actions for each visible post
        if ($reader) {
            foreach ($posts as $post) {
                $reader->recordAction(ContentTrigger::TYPE_VIEW_POST, 'post', $post->id);
            }

            // Record visit to this thread
            $reader->recordVisit('thread', $thread->id);

            // Check phase progress after recording actions
            app(PhaseProgressService::class)->checkProgress($reader);
        }

        $trendingThreads = Thread::with(['author', 'category'])
            ->visibleTo($reader)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        return view('forum.thread', compact('category', 'thread', 'posts', 'reader', 'trendingThreads'));
    }

    public function profile(Character $character)
    {
        $reader = $this->getReader();

        $character->load('role');

        $posts = $character->posts()
            ->with('thread')
            ->visibleTo($reader)
            ->orderByDesc('fake_created_at')
            ->take(config('pagination.profile_posts'))
            ->get();

        return view('forum.profile', compact('character', 'posts', 'reader'));
    }

    public function messages()
    {
        $reader = $this->getReader();

        // Get character-to-character archived messages (existing behavior)
        $archivedMessages = PrivateMessage::with(['sender', 'recipient'])
            ->archivedMessages()
            ->visibleTo($reader)
            ->orderByDesc('fake_sent_at')
            ->paginate(config('pagination.forum'));

        // Get reader's personal inbox if logged in
        $inboxMessages = null;
        if ($reader) {
            $inboxMessages = PrivateMessage::with(['sender'])
                ->inboxMessages()
                ->visibleTo($reader)
                ->orderByDesc('fake_sent_at')
                ->paginate(config('pagination.forum'), ['*'], 'inbox_page');
        }

        return view('forum.messages', compact('archivedMessages', 'inboxMessages'));
    }

    public function message(PrivateMessage $message)
    {
        $reader = $this->getReader();

        // Check if message is accessible
        if (!$message->isVisibleToReader($reader)) {
            abort(404);
        }

        // Mark as read if reader views an inbox message
        if ($reader && $message->is_inbox_message && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('forum.message', compact('message'));
    }
}
