<x-forum-layout>
    <x-slot name="title">{{ $category->name }} - {{ config('app.name') }}</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 text-sm" style="color: var(--color-text-muted);">
        <a href="{{ route('forum.index') }}" class="hover:opacity-80" style="color: var(--color-accent);">Forums</a>
        <span class="mx-2">›</span>
        <span>{{ $category->name }}</span>
    </div>

    <div class="mb-8">
        <h1 class="text-2xl font-medium mb-2" style="color: var(--color-text);">{{ $category->name }}</h1>
        @if($category->description)
            <p style="color: var(--color-text-muted);">{{ $category->description }}</p>
        @endif
    </div>

    <!-- Threads List -->
    <div class="rounded-lg overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        @forelse($threads as $thread)
            @php
                $newPostCount = $threadNewCounts[$thread->id] ?? 0;
                $hasNewPosts = $newPostCount > 0;
            @endphp
            <a href="{{ route('forum.thread', [$category, $thread]) }}" 
               class="block p-5 transition-all duration-200"
               style="border-bottom: 1px solid var(--color-border);"
               onmouseover="this.style.backgroundColor='var(--color-surface-hover)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            @if($thread->is_pinned)
                                <span class="text-xs px-2 py-0.5 rounded" style="background-color: var(--color-accent); color: var(--color-bg);">Pinned</span>
                            @endif
                            @if($thread->is_locked)
                                <span class="text-xs px-2 py-0.5 rounded" style="background-color: var(--color-border); color: var(--color-text-muted);">Locked</span>
                            @endif
                            @if($hasNewPosts)
                                <span class="text-xs px-2 py-0.5 rounded animate-pulse" style="background-color: var(--color-accent-warm); color: var(--color-bg);">
                                    {{ $newPostCount }} new
                                </span>
                            @endif
                            <h3 class="font-medium" style="color: var(--color-text);">{{ $thread->title }}</h3>
                        </div>
                        <div class="flex items-center gap-4 text-sm" style="color: var(--color-text-muted);">
                            <span>by <span style="color: var(--color-accent);">{{ $thread->author->display_name }}</span></span>
                            <span>{{ $thread->fake_created_at?->diffForHumans() ?? 'Unknown date' }}</span>
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        @php $visiblePosts = $threadVisiblePostCounts[$thread->id] ?? $thread->posts->count(); @endphp
                        <span class="text-lg" style="color: var(--color-text);">{{ max(0, $visiblePosts - 1) }}</span>
                        <span class="block text-xs" style="color: var(--color-text-muted);">replies</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-8 text-center" style="color: var(--color-text-muted);">
                No threads in this category yet.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $threads->links() }}
    </div>
</x-forum-layout>
