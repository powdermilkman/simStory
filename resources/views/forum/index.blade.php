<x-forum-layout>
    <x-slot name="title">{{ config('app.name') }} - Simulation Discussion Forums</x-slot>

    <div class="mb-8">
        <h1 class="text-3xl font-light mb-2" style="color: var(--color-text);">Welcome to {{ config('app.name') }}</h1>
        <p style="color: var(--color-text-muted);">Share your experiences, discuss simulations, connect with fellow enthusiasts.</p>
    </div>

    <div class="space-y-4">
        @foreach($categories as $category)
            @php
                $newInfo = $categoryNewCounts[$category->id] ?? null;
                $hasNew = $newInfo && ($newInfo['new_threads'] > 0 || $newInfo['threads_with_new_posts'] > 0);
            @endphp
            <a href="{{ route('forum.category', $category) }}" 
               class="block rounded-lg p-6 transition-all duration-200"
               style="background-color: var(--color-surface); border: 1px solid var(--color-border);"
               onmouseover="this.style.backgroundColor='var(--color-surface-hover)'"
               onmouseout="this.style.backgroundColor='var(--color-surface)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-xl font-medium" style="color: var(--color-text);">{{ $category->name }}</h2>
                            @if($hasNew)
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full animate-pulse" 
                                      style="background-color: var(--color-accent); color: var(--color-bg);">
                                    NEW
                                </span>
                            @endif
                        </div>
                        @if($category->description)
                            <p class="text-sm" style="color: var(--color-text-muted);">{{ $category->description }}</p>
                        @endif
                        @if($hasNew)
                            <p class="text-xs mt-2" style="color: var(--color-accent);">
                                @if($newInfo['new_threads'] > 0)
                                    {{ $newInfo['new_threads'] }} new thread{{ $newInfo['new_threads'] > 1 ? 's' : '' }}
                                @endif
                                @if($newInfo['new_threads'] > 0 && $newInfo['threads_with_new_posts'] > 0)
                                    · 
                                @endif
                                @if($newInfo['threads_with_new_posts'] > 0)
                                    {{ $newInfo['threads_with_new_posts'] }} thread{{ $newInfo['threads_with_new_posts'] > 1 ? 's' : '' }} with new posts
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="text-right ml-4">
                        <span class="text-2xl font-light" style="color: var(--color-accent);">{{ $category->threads_count }}</span>
                        <span class="block text-xs" style="color: var(--color-text-muted);">threads</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-12 rounded-lg" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <p style="color: var(--color-text-muted);">No forums available yet. Check back soon!</p>
        </div>
    @endif
</x-forum-layout>
