<div class="flex flex-wrap gap-2">
    @foreach($phase->gatedThreads as $thread)
        <a href="{{ route('admin.threads.show', $thread) }}" class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded hover:bg-violet-100">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            Thread: {{ $thread->title }}
        </a>
    @endforeach
    @foreach($phase->gatedPosts as $post)
        <a href="{{ route('admin.posts.show', $post) }}" class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded hover:bg-violet-100">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Post #{{ $post->id }}{{ $post->thread ? ' in "' . $post->thread->title . '"' : '' }}
        </a>
    @endforeach
    @foreach($phase->gatedMessages as $message)
        <a href="{{ route('admin.private-messages.show', $message) }}" class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-800 border border-violet-200 px-2 py-1 rounded hover:bg-violet-100">
            <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Message: "{{ $message->subject }}"{{ $message->sender ? ' from ' . $message->sender->display_name : '' }}
        </a>
    @endforeach
</div>
